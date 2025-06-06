# LabCorreo - Sistema de Gestión de Solicitudes (PQRS)

[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red)](https://laravel.com/)
[![PHP Version](https://img.shields.io/badge/PHP-8.2-blue)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/Database-MySQL-green)](https://www.mysql.com/)

---

## Descripción

LabCorreo es un sistema desarrollado en Laravel 12 para la gestión integral de solicitudes tipo PQRS (Peticiones, Quejas, Reclamos, Sugerencias), que permite registrar, administrar y controlar el estado de las solicitudes recibidas por distintos medios.

El sistema incluye:

- Gestión de tipos de solicitud, medios de recepción y estados.
- Roles y permisos para usuarios (Administrador, Gestor de grupos, Miembro de grupo).
- Actualización automática diaria del estado de las solicitudes basada en días hábiles transcurridos.
- Cálculo de días hábiles considerando festivos nacionales, obtenidos desde una API externa y almacenados en caché para optimizar rendimiento.

---

## Tecnologías

- PHP 8.2+
- Laravel Framework 12.x
- MySQL
- Composer
- API externa para festivos: `https://api.generadordni.es/v2/holidays/holidays`

---


## Instalación

1. **Clonar repositorio**

```bash
git clone https://github.com/xamtam54/LabCorreo.git
cd LabCorreo
```

2. **Instalar dependencias**

```bash
composer install
```

3. **Configurar variables de entorno**

```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` para configurar la base de datos MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=labcorreo
DB_USERNAME=root
DB_PASSWORD=tu_contraseña
```

4. **Migrar y sembrar la base de datos**

```bash
php artisan migrate --seed
```

Esto crea la estructura y carga datos iniciales como tipos de solicitud, medios, estados y roles.

---

## Uso

### Roles y usuarios

Roles por defecto: `Administrador`, `Gestor_grupos`, `Miembro_grupo`.

Usuario administrador predeterminado:

- **Email:** admin@admin
- **Contraseña:** contraseña123

El administrador tiene permisos completos para gestionar usuarios, roles y grupos.

### Actualización automática de estados

El sistema incluye un comando Artisan que actualiza diariamente el estado de las solicitudes en función de los días hábiles transcurridos desde su creación.

Ejecutar manualmente:

```bash
php artisan solicitudes:actualizar-estados
```

Para programar la ejecución automática diaria, agregar en `routes/Console.php`:

```php
Schedule::command('solicitudes:actualizar-estados')->daily();
```

### Cálculo de días hábiles y festivos

Se utiliza un servicio que consulta la API pública de festivos nacionales para Colombia, almacenando los festivos en caché por dos años.

Este servicio permite calcular el número de días hábiles entre dos fechas, considerando fines de semana y festivos.

---

## Estructura principal de la base de datos

| Tabla            | Descripción                                                      |
|------------------|-----------------------------------------------------------------|
| tipo_solicitud   | Tipos de solicitudes PQRS                                        |
| medio_recepcion  | Canales o medios por donde se reciben las solicitudes           |
| estado_solicitud | Estados posibles para las solicitudes (ej. Pendiente, Cerrado)  |
| roles            | Roles de usuarios del sistema                                   |
| users            | Tabla estándar de Laravel para autenticación y control de acceso. Contiene credenciales y datos básicos de login. |
| usuarios         | Datos adicionales de los usuarios vinculada a `users`. |
| solicitud        | Registro principal de solicitudes PQRS                          |
| documento        | Documentos adjuntos a solicitudes                               |
| grupos           | Grupos de usuarios dentro del sistema                           |
| grupo_usuario    | Relación entre usuarios y grupos, con roles y estados (bloqueos)|


---

## Licencia

Este proyecto está bajo licencia MIT. Consulta el archivo LICENSE para más detalles.

---

## Contacto

Para soporte o preguntas, utiliza GitHub Issues.

¡Gracias por usar LabCorreo!
