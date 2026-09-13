# Changelog

## 2.1.0 — 2026-09-13

Arreglos para que el paquete funcione en una aplicación Laravel 13 recién creada, con un sitio que lee las opciones sin sesión y un panel que las edita. Rutas, nombres de ruta, tabla y claves de configuración no cambian.

### Corregido

- **`php artisan migrate` en una aplicación nueva.** Los proveedores ya no leen la caché al arrancar. Con `CACHE_STORE=database`, el valor por defecto de Laravel, consultaban la tabla `cache` antes de que la primera migración la creara y `migrate` fallaba. Desaparecen las claves genéricas `options_auth_policies` y `laravel_options_events_and_observers`, que otros paquetes podían pisar.
- **Política.** `OptionPolicy` se registra explícitamente para `Option`. El descubrimiento armaba mal el nombre del modelo y solo funcionaba si Laravel adivinaba la política por convención; con `Gate::guessPolicyNamesUsing()` toda escritura respondía 403, también a un administrador.
- **Política con otros usuarios.** Se tipa con el contrato `Authenticatable` en vez de `App\Models\User` y comprueba que `isAdmin()` exista. Antes, quien no administraba recibía un 500 (TypeError) en vez de un 403.
- **Borrado permanente.** `force-delete` ya no hace un `abort(403)` escondido en el modelo: lo decide `OptionPolicy`, donde nace apagado, también para administradores.
- **Proveedores.** `AuthServiceProvider`, `EventServiceProvider` y `RouteServiceProvider` extienden el `ServiceProvider` base. El `EventServiceProvider` de Laravel añadía `SendEmailVerificationNotification` y el correo de verificación salía repetido; el `RouteServiceProvider` de Laravel volvía a cargar las rutas de la aplicación. Las rutas del paquete se saltan cuando la aplicación las tiene cacheadas.
- **Exportación.** Lee `laravel-options.*` en vez de `innoboxrrlaraveloptions.*`, así que encuentra su vista. El archivo se genera una sola vez sea cual sea el canal, con un nombre seguro y un enlace de descarga real. Si una configuración publicada trae el prefijo viejo de la vista, se usa la del paquete.
- **Exportación sin Excel.** Sin `maatwebsite/excel` responde `501` con el comando para instalarlo; cualquier otro fallo se reporta y devuelve un mensaje legible.
- **`options:seed`.** Solo crea las claves que faltan (`firstOrCreate`): ya no devuelve a su valor por defecto lo que se cambió desde el panel. Una opción borrada no hace fallar el seeder ni se revive. Usa `--force`, así que siembra también en producción, donde antes se cancelaba y decía que había sembrado.
- **`OptionFactory`** genera filas válidas, con `key` única.
- **`OptionAssignment`** queda como ejemplo comentado: llamaba a una relación `models()` que no existe.
- **Validación.** `value` admite un arreglo, que se guarda como JSON, y un valor vacío. Actualizar con `key` nula devuelve 422 en vez de un 500.
- **Dependencias.** `innoboxrr/traits` (^2.1) e `innoboxrr/search-surge` (^3.0) pasan a `require`; solo llegaban por una dependencia de desarrollo.

### Añadido

- `Option::value(string $key, mixed $default = null)`: el valor de una clave, con los objetos y arreglos JSON decodificados.
- Tests con Testbench de lectura pública, `paginate=0`, 401/403 en escrituras, operaciones de administrador, arranque con la caché en base de datos, exportación, seeder y validación.

### A tener en cuenta al actualizar

- Cambian valores por defecto de la configuración: `export_disk` pasa de `s3` a `env('LARAVEL_OPTIONS_EXPORT_DISK', 'local')`, `notification_via` de `['mail', 'database']` a `['mail']` y `excel_view` de `innoboxrrlaraveloptions::excel.` a `laravel-options::excel.`. Una configuración ya publicada conserva sus valores: revisa `export_disk` y `notification_via`.
- `Option::value()` es un método propio. Antes `Option::value('columna')` llegaba al query builder y devolvía esa columna de la primera fila.
- `options:seed` ya no restaura los valores por defecto de las claves que existen.
- `laravel/sanctum` pasa a sugerido y `maatwebsite/excel` se sugiere como `^3.1 || ^4.0`.
