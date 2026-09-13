<?php

namespace Innoboxrr\LaravelOptions\Notifications\Option;

use Maatwebsite\Excel\Facades\Excel;
use Innoboxrr\LaravelOptions\Exports\OptionsExports;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExportNotification extends Notification
{

    use Queueable;

    private string $path;

    private bool $exported = false;

    public function __construct(private array $data)
    {
        $this->path = $this->getPath();
    }

    /**
     * Por correo, que en una aplicacion nueva ya funciona. El canal database
     * necesita la tabla de notificaciones: sin ella la exportacion fallaba.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return config('laravel-options.notification_via', ['mail']);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->createExport();

        return (new MailMessage)
                    ->subject($this->getSubject())
                    ->greeting($this->getWellcomeMessage())
                    ->line($this->getBodyMessage())
                    ->action($this->getActionButton(), $this->getDownloadUrl())
                    ->line($this->getFarewallMessage());
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->createExport();

        return [
            'action' => $this->getDownloadUrl(),
            'message' => $this->getBodyMessage(),
            'img' => $this->getImg(),
        ];
    }

    // CUSTOME METHODS

    /**
     * Una sola vez aunque la notificacion salga por varios canales. Antes el
     * archivo solo se creaba al enviar el correo: por database se avisaba de
     * un archivo que no existia.
     */
    private function createExport(): void
    {
        if ($this->exported) {
            return;
        }

        Excel::store(new OptionsExports($this->data), $this->path, $this->disk());

        $this->exported = true;
    }

    /**
     * local por defecto, que toda aplicacion tiene. Antes se leia de una
     * clave de configuracion que no existia y caia siempre en s3, que hay que
     * configurar: exportar fallaba recien instalado el paquete.
     */
    private function disk(): string
    {
        return config('laravel-options.export_disk', 'local');
    }

    private function getSubject(): string
    {
        return config('app.name') . ' | Exportación de opciones';
    }

    private function getWellcomeMessage(): string
    {
        return 'Hola';
    }

    private function getBodyMessage(): string
    {
        return 'Da clic para descargar el archivo. Después de 24 horas será eliminado.';
    }

    private function getActionButton(): string
    {
        return 'Descargar';
    }

    /**
     * Un enlace firmado que caduca cuando el disco lo permite, y si no la URL
     * del disco. Antes el correo apuntaba a /notification/read/{id}, una ruta
     * que nadie define, y la notificacion a app.aws_url, que solo existe con S3.
     */
    private function getDownloadUrl(): string
    {
        $disk = Storage::disk($this->disk());

        try {
            return $disk->temporaryUrl($this->path, now()->addDay());
        } catch (\RuntimeException) {
            return $disk->url($this->path);
        }
    }

    private function getImg(): string
    {
        return 'https://www.gravatar.com/avatar';
    }

    private function getFarewallMessage(): string
    {
        return '¡Gracias por utilizar nuestra aplicación!';
    }

    /**
     * base64 de microtime() podia llevar / y =, que en un disco son carpetas
     * y caracteres raros.
     */
    private function getPath(): string
    {
        return 'exports/' . Str::uuid() . '.xlsx';
    }

}
