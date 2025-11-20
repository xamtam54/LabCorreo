<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Crear tabla de tipos de remitente
        Schema::create('tipo_remitente', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100); // Ej: Persona natural, Entidad, Anónimo
            $table->timestamps();
        });

        // Crear tabla de tipos de documento de identificación
        Schema::create('tipo_documento_identificacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50); // Ej: Cédula, NIT, Pasaporte
            $table->string('abreviatura', 10)->nullable();
            $table->timestamps();
        });

        // Crear tabla de remitentes (caracterización básica)
        Schema::create('remitente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_remitente_id')
                ->constrained('tipo_remitente')
                ->cascadeOnDelete();

            $table->foreignId('tipo_documento_identificacion_id')
                ->nullable()
                ->constrained('tipo_documento_identificacion')
                ->nullOnDelete();

            $table->string('nombre', 255)->nullable();
            $table->string('numero_documento', 50)->nullable();
            $table->string('correo', 255)->nullable();
            $table->timestamps();
        });

        // Modificar tabla de solicitud
        Schema::table('solicitud', function (Blueprint $table) {

            // 1️⃣ Eliminar el campo anterior de remitente (texto)
            if (Schema::hasColumn('solicitud', 'remitente')) {
                $table->dropColumn('remitente');
            }

            // 2️⃣ Eliminar relación anterior con documento (si existe) --
            //     CORRECCIÓN: la columna antigua es 'documento_adjunto_id'
            if (Schema::hasColumn('solicitud', 'documento_adjunto_id')) {
                // intentar eliminar la FK (si existe)
                try {
                    $table->dropForeign(['documento_adjunto_id']);
                } catch (\Throwable $e) {
                    // Si la FK no existe con el nombre esperado, ignoramos el error
                }
                $table->dropColumn('documento_adjunto_id');
            }

            $table->foreignId('remitente_id')
                ->nullable()
                ->constrained('remitente')
                ->nullOnDelete();
            $table->foreignId('tipo_remitente_id')
                ->nullable()
                ->constrained('tipo_remitente')
                ->nullOnDelete();
        });

        // Crear tabla pivote solicitud-documento (nueva relación)
        Schema::create('solicitud_documento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')
                ->constrained('solicitud')
                ->cascadeOnDelete();

            $table->foreignId('documento_id')
                ->constrained('documento')
                ->cascadeOnDelete();

            $table->integer('orden')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_documento');

        Schema::table('solicitud', function (Blueprint $table) {
            // Quitar relación con remitente si existe
            if (Schema::hasColumn('solicitud', 'remitente_id')) {
                try {
                    $table->dropForeign(['remitente_id']);
                } catch (\Throwable $e) {
                    // ignorar si no existe la FK
                }
                $table->dropColumn('remitente_id');
            }

            // Restaurar columnas previas (si no existen ya)
            if (! Schema::hasColumn('solicitud', 'remitente')) {
                $table->string('remitente', 255)->nullable();
            }

            if (! Schema::hasColumn('solicitud', 'documento_adjunto_id')) {
                $table->foreignId('documento_adjunto_id')->nullable()->constrained('documento')->nullOnDelete();
            }
        });

        Schema::dropIfExists('remitente');
        Schema::dropIfExists('tipo_documento_identificacion');
        Schema::dropIfExists('tipo_remitente');
    }
};
