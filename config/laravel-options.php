<?php

return [

	'user_class' => 'App\Models\User',

	// Las vistas del paquete se cargan con el prefijo laravel-options::.
	'excel_view' => 'laravel-options::excel.',

	// Por dónde avisa la exportación. `database` necesita la tabla de
	// notificaciones de la aplicación: php artisan make:notifications-table
	'notification_via' => ['mail'],

	// Dónde se guarda el archivo exportado. `local` funciona en cualquier
	// aplicación; en producción, normalmente `s3`.
	'export_disk' => env('LARAVEL_OPTIONS_EXPORT_DISK', 'local'),

];
