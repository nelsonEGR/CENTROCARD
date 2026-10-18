# Centrocard

Sistema POS construido con CodeIgniter 4 y PostgreSQL (Neon).

## Inicio local

El proyecto solo necesita Docker Desktop. No requiere Laragon, PHP, Composer ni
PostgreSQL instalados en Windows.

1. Copia `.env.example` a `.env` y completa la conexion a tu base de datos
   (Neon u otro servidor PostgreSQL accesible por red).
2. Abre PowerShell como Administrador en la raiz del proyecto.
3. Ejecuta `powershell -ExecutionPolicy Bypass -File .\docker\setup-local.ps1`.
4. Abre `http://centrocar.test/`.

El script registra `centrocar.test` en el archivo `hosts`, construye la imagen e
inicia los contenedores. El dominio `.test` funciona sobre HTTP en el puerto
`80`, por eso no se usa HTTPS ni certificados locales.

Credenciales iniciales: `admin` / `password`. Cambia la contrasena despues de
iniciar sesion.

## Servicios

- `app`: PHP 8.3, Apache y CodeIgniter, publicado en `http://centrocar.test/`.

La aplicacion no depende de una base de datos local: se conecta a la instancia
configurada en `.env` (por ejemplo, Neon en linea).

Para detener el entorno usa `docker compose down`. Los datos de ejecucion
(sesiones, cache, imagenes de productos) se guardan en volumenes Docker, fuera
del arbol del repositorio.

Si Docker informa que el puerto `80` esta ocupado, deten el servicio que lo usa
(por ejemplo, un Apache/nginx de Laragon) antes de ejecutar el script.

## Variables de entorno

La conexion a la base de datos se controla por variables de entorno. El
contenedor las recibe en tiempo de ejecucion y genera el `.env` interno de
CodeIgniter en el arranque (`docker/entrypoint.sh`), de modo que el repositorio
no contiene credenciales.

| Variable         | Descripcion                                   |
| ---------------- | --------------------------------------------- |
| `CI_ENVIRONMENT` | Entorno: `development`, `testing`, `production` |
| `APP_BASE_URL`   | URL base de la aplicacion                     |
| `DB_HOSTNAME`    | Host de PostgreSQL (endpoint de Neon)         |
| `DB_DATABASE`    | Nombre de la base de datos                    |
| `DB_USERNAME`    | Usuario de la base de datos                   |
| `DB_PASSWORD`    | Contrasena de la base de datos                |
| `DB_PORT`        | Puerto (por defecto `5432`)                   |
| `DB_SCHEMA`      | Esquema (por defecto `public`)                |
| `DB_SSLMODE`     | Modo SSL (`require` en Neon)                  |
| `DB_OPTIONS`     | Opciones extra del DSN (endpoint del pooler)  |
| `IMPUESTO`       | Porcentaje de impuesto                        |
| `GANANCIA`       | Porcentaje de ganancia                        |
| `RUN_MIGRATIONS` | Ejecuta migraciones y seeder al arrancar      |

Nunca publiques `.env`, dumps, sesiones, logs o archivos de `writable`.

## Despliegue en contenedor

La imagen es autocontenida. Para publicarla en cualquier host que entienda
Docker, pasa la configuracion como variables de entorno:

```bash
docker build -t centrocard:latest .
docker run -d -p 80:80 \
  -e APP_BASE_URL=https://mi-dominio.com/ \
  -e CI_ENVIRONMENT=production \
  -e DB_HOSTNAME=tu-endpoint.neon.tech \
  -e DB_DATABASE=centrocard \
  -e DB_USERNAME=usuario \
  -e DB_PASSWORD=secreto \
  -e DB_SSLMODE=require \
  centrocard:latest
```

Para Render se conserva `render.yaml`; configura las credenciales en el panel de
Render, no en el repositorio.

## Verificacion

En una maquina con las herramientas instaladas:

```powershell
docker compose config
docker compose up --build --detach
composer validate --strict
composer test
```

El workflow `.github/workflows/verify.yml` instala las dependencias de
desarrollo y ejecuta la validacion de Composer y PHPUnit en cada push y pull
request.
