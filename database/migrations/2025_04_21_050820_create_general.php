<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
                // Crear tabla 'usuarios' (id igual a 'users')
                Schema::create('usuarios', function (Blueprint $table) {
                    $table->unsignedBigInteger('id')->primary();
                    $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
                    $table->string('nombres', 100);
                    $table->string('apellidos', 100);
                    $table->date('fecha_creacion');
                    $table->date('fecha_ultima_sesion');
                    $table->rememberToken();
                    $table->timestamps();
                    $table->softDeletes();
                });
                // Tabla para los tipos de solicitud (puede ser queja, sugerencia, etc.)
                Schema::create('tipo_solicitud', function (Blueprint $table) {
                    $table->id();
                    $table->string('nombre', 100); // Queja, Sugerencia, Agradecimiento
                });

                // Tabla para los medios de recepción (puede ser correo, formulario web, etc.)
                Schema::create('medio_recepcion', function (Blueprint $table) {
                    $table->id();
                    $table->string('nombre', 100); // Correo, Web, Teléfono
                });

                // Tabla para los tipos de documento (relacionado con los documentos adjuntos)
                Schema::create('tipo_documento', function (Blueprint $table) {
                    $table->id();
                    $table->string('nombre', 100); // PDF, Imagen, etc.
                    $table->text('descripcion')->nullable();
                });

                // Tabla para los documentos (archivos adjuntos a la solicitud)
                Schema::create('documento', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('editor_id')->constrained('usuarios')->onDelete('cascade');
                    $table->string('nombre_archivo', 255);
                    $table->foreignId('tipo_documento_id')->constrained('tipo_documento');
                    $table->float('tamano_mb');
                    $table->string('ruta', 255);
                    $table->softDeletes();
                });

                // Tabla para los estados de solicitud (en espera, respondido, etc.)
                Schema::create('estado_solicitud', function (Blueprint $table) {
                    $table->id();
                    $table->string('nombre', 100); // Pendiente, Respondido, Cerrado
                    $table->text('descripcion')->nullable();
                });

                // Tabla para las solicitudes
                Schema::create('solicitud', function (Blueprint $table) {
                    $table->id();
                    $table->string('numero_radicado', 100)->unique(); // Número único de radicado
                    $table->foreignId('tipo_solicitud_id')->constrained('tipo_solicitud');
                    $table->string('remitente', 255);
                    $table->text('asunto')->nullable();
                    $table->foreignId('medio_recepcion_id')->constrained('medio_recepcion');
                    $table->date('fecha_ingreso');
                    $table->foreignId('documento_adjunto_id')->nullable()->constrained('documento'); // Si tiene documento adjunto
                    $table->date('fecha_vencimiento')->nullable(); // Se calcula con lógica en el backend
                    $table->foreignId('usuario_id')->constrained('usuarios');
                    $table->foreignId('estado_id')->constrained('estado_solicitud'); // Relación con el estado de la solicitud
                    $table->boolean('firma_digital')->default(false); // Si tiene firma digital
                    $table->timestamps();
                    $table->softDeletes();
                });

                // Tabla para los semáforos (gestiona los estados y plazos)
                Schema::create('semaforo', function (Blueprint $table) {
                    $table->id();
                    $table->string('estado', 100); // En espera, Vencido, Respondido
                    $table->integer('tiempo_restante_horas'); // Horas restantes para la fecha de vencimiento
                    $table->integer('plazo_horas'); // Plazo total en horas
                    $table->foreignId('solicitud_id')->constrained('solicitud'); // Relación con la solicitud
                });

                // Tabla para las notificaciones (mensajes enviados a los usuarios)
                Schema::create('notificacion', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('usuario_id')->constrained('usuarios');
                    $table->text('mensaje');
                    $table->dateTime('fecha_envio');
                    $table->boolean('leida');
                    $table->string('tipo', 100); // Tipo de notificación (Correo, Recordatorio, etc.)
                });

                // Tabla para los reportes (relacionados con solicitudes)
                Schema::create('reporte', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('autor_id')->constrained('usuarios');
                    $table->string('tipo', 100);
                    $table->text('contenido');
                    $table->foreignId('solicitud_relacionada_id')->constrained('solicitud');
                    $table->date('fecha_generacion');
                    $table->softDeletes();
                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte');
        Schema::dropIfExists('notificacion');
        Schema::dropIfExists('semaforo');
        Schema::dropIfExists('solicitud');
        Schema::dropIfExists('estado_solicitud');
        Schema::dropIfExists('documento');
        Schema::dropIfExists('tipo_documento');
        Schema::dropIfExists('medio_recepcion');
        Schema::dropIfExists('tipo_solicitud');
        Schema::dropIfExists('usuarios');
    }
};
