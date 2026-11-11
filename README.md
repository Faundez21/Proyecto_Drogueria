# Droguería DAS Talcahuano

Plataforma web para la gestión de la Droguería Comunal de la Dirección de Administración de Salud (DAS) de Talcahuano. Cubre el recorrido completo de los medicamentos e insumos: desde que llegan a la bodega hasta que se despachan a los centros de salud, con control por lote, vencimientos y trazabilidad.

Proyecto de práctica de Ingeniería de Ejecución en Informática (UCSC, Instituto Tecnológico).

## Qué hace

- **Recepción** de mercadería por documento (factura, guía, etc.), con lotes, vencimientos, valores y firma del receptor. Genera un acta en PDF.
- **Inventario** por producto y lote, con valorización, ubicación en bodega, etiquetas QR y carga masiva desde Excel.
- **Cuarentena y mermas.** Los lotes vencidos pasan solos a Merma.
- **Solicitudes** de los establecimientos de la red (CESFAM, CECOSF, SAR, postas, etc.), con firma del solicitante e impresión en PDF.
- **Despacho normal** y **despacho controlado** (estupefacientes, solo para CESFAM). Ambos descuentan stock por FEFO (primero vence, primero sale) y generan guía en PDF.
- **Distribución** de la bodega en 2D/3D: pasillos, estanterías, niveles, posiciones y porcentaje de ocupación.
- **Trazabilidad** de cada movimiento por lote y **reportes** valorizados en Excel y PDF.
- **Usuarios y roles**, con acceso por vista.

## Tecnologías

- PHP 8.3+ con Laravel 13
- MySQL 8
- Blade, Tailwind CSS y Alpine.js (compilados con Vite)
- spatie/laravel-permission, maatwebsite/excel, barryvdh/laravel-dompdf, simplesoftwareio/simple-qrcode
- Three.js, SignaturePad, SweetAlert2 y html5-qrcode en el front

## Requisitos

- PHP 8.3 o superior, con las extensiones habituales de Laravel más `gd`, `zip` y `pdo_mysql`
- Composer 2
- Node.js 18 o superior y npm
- MySQL 8 (o MariaDB)

## Instalación

```bash
git clone https://github.com/Faundez21/Proyecto_Drogueria.git
cd Proyecto_Drogueria

composer install
npm install

cp .env.example .env
php artisan key:generate
```

En el `.env` cambia la conexión a MySQL (por defecto viene SQLite):

```env
APP_NAME="Droguería DAS"
APP_LOCALE=es

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=drogueria
DB_USERNAME=root
DB_PASSWORD=
```

Crea la base de datos vacía y luego:

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

La aplicación queda en http://127.0.0.1:8000.

El seeder solo crea los roles, los permisos y un usuario administrador (los datos están en `database/seeders/UserSeeder.php`). Cambia esa contraseña después del primer ingreso.

Para borrar todo y partir de cero: `php artisan migrate:fresh --seed`.

## Primeros pasos en la plataforma

La base queda vacía a propósito. El orden recomendado es:

1. **Proveedores:** registrar los proveedores. El RUT se valida con su dígito verificador.
2. **Distribución:** crear los pasillos, estanterías, niveles y posiciones de la bodega.
3. **Inventario → Carga Masiva:** descargar la plantilla, llenarla y subirla. Al terminar, la plataforma muestra un resumen de lo que cargó y de las filas que omitió.
   - Los productos vencidos se cargan directo en Merma.
   - Las filas sin cantidad o con un estado inválido se omiten.
   - Las ubicaciones que no existen quedan como "Sin asignar (P.A)".
4. **Usuarios y Permisos:** crear los roles con sus vistas y luego los usuarios. Un rol sin vistas no se puede asignar.

## Configuración propia del sistema

Todo está en `config/drogueria.php`:

- `iva`: porcentaje de IVA aplicado en recepción.
- `meses_alerta_vencimiento`: desde cuándo un lote se marca como "pronto a vencer".
- `controlados`: lista oficial de estupefacientes.
- `principios_controlados`: principios activos controlados. Cualquier producto que los contenga en su nombre se trata como controlado, aunque esté escrito distinto en el catálogo.
- `establecimientos`: establecimientos de la red, con su tipo (CESFAM u Otros) y dirección.

## Vencimientos automáticos

Los lotes vencidos se envían a Merma de dos formas:

- Mientras la plataforma se usa, como máximo cada 5 minutos.
- Con el comando `php artisan inventario:vencidos`, programado todos los días a las 00:05.

Para que la tarea diaria corra en el servidor, hay que agregar el programador de Laravel al cron:

```
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

## Puesta en producción

- Usar Apache o Nginx con PHP-FPM. `php artisan serve` es solo para desarrollo: atiende una petición a la vez.
- HTTPS. El escáner QR usa la cámara y el navegador solo lo permite con HTTPS (o en localhost).
- En el `.env`: `APP_ENV=production` y `APP_DEBUG=false`.
- En el `php.ini`:
  - `max_input_vars = 10000`, porque las recepciones con muchos productos superan el límite por defecto de 1000.
  - `memory_limit = 512M`, para la generación de PDF.
- Después de cada actualización:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

- Respaldar la base de datos todos los días (`mysqldump`).

## Estructura

```
app/
  Http/Controllers/     un controlador por módulo
  Http/Middleware/      EnsureUserIsActive, MoverVencidosAMerma
  Imports/              carga masiva de inventario
  Exports/              exportación del reporte a Excel
  Models/
  Services/
    InventoryService.php   FEFO, ingresos, movimientos, vencidos, ubicaciones
    SolicitudService.php   despachos contra solicitudes y estados
  Support/Rut.php       validación de RUT
config/drogueria.php    configuración propia (ver arriba)
database/
  migrations/
  seeders/              roles, permisos y usuario administrador
resources/views/        vistas Blade por módulo
routes/web.php          rutas y permisos por módulo
routes/console.php      comando inventario:vencidos
```

## Estado actual y pendientes

La plataforma funciona con el volumen actual de la droguería. Para más adelante quedan anotados:

- Las vistas de Despacho y Solicitudes cargan todo el historial de solicitudes y despachos. Con varios cientos de registros conviene paginarlas.
- El PDF consolidado de solicitudes pendientes se vuelve pesado con muchas solicitudes; filtrar por centro o fechas lo resuelve.
- El guardado de solicitudes debería ir dentro de una transacción.
- Los resultados de la búsqueda global deberían escapar el HTML de los nombres.
