# Centrocard

Sistema POS construido con CodeIgniter 4 y PostgreSQL.

## Inicio local

El proyecto local no necesita Laragon, PHP, Composer ni PostgreSQL instalados en Windows. Solo requiere Docker Desktop con los puertos `80` y `443` libres.

1. Clona el repositorio y abre PowerShell como Administrador en la raiz del proyecto.
2. Ejecuta `powershell -ExecutionPolicy Bypass -File .\docker\setup-local.ps1`.
3. Abre `https://centrocar.dev`.

El script registra `centrocar.dev` en el archivo `hosts`, inicia Docker Compose e instala en Windows el certificado local de Caddy. El dominio `.dev` requiere HTTPS, por eso no funciona de forma fiable con HTTP plano.

Credenciales iniciales de desarrollo: `admin` / `password`. Cambia la contrasena despues de iniciar sesion. Esos datos solo existen en la base de datos local creada por Docker.

## Servicios locales

- `proxy`: Caddy con HTTPS local para `centrocar.dev`.
- `app`: PHP 8.3, Apache y CodeIgniter.
- `database`: PostgreSQL 16 local.

La primera ejecucion aplica la migracion y carga datos de desarrollo sin usar el dump legado ni una base Neon. Los datos, sesiones, cargas de productos y certificados se guardan en volumenes Docker, fuera del arbol del repositorio.

Para detener el entorno usa `docker compose down`. Para eliminar todos los datos locales usa `docker compose down --volumes` y vuelve a ejecutar el script de inicio. Este ultimo comando borra la base de datos local.

Si Docker informa que los puertos `80` o `443` estan ocupados, detiene el servicio que los usa antes de ejecutar el script. No es necesario desinstalar Laragon, pero este proyecto ya no depende de el.

## Configuracion y despliegue

Docker Compose define una configuracion local segura para desarrollo y no lee el `.env` local. `.env.example` documenta esos valores para otros entornos. Nunca publiques `.env`, dumps, sesiones, logs o archivos de `writable`.

Para Render se conserva `render.yaml`. Configura las credenciales de PostgreSQL y la URL de produccion en el panel de Render; no uses las credenciales ni los datos locales de desarrollo en produccion.

## Verificacion

En una maquina con las herramientas instaladas, ejecuta:

```powershell
docker compose config
docker compose up --build --detach
composer validate --strict
composer test
```

El workflow `.github/workflows/verify.yml` instala las dependencias de desarrollo y ejecuta la validacion de Composer y PHPUnit en cada push y pull request.
