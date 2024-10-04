<?php

namespace Innoboxrr\LaravelOptions\Http\Events\Option\Listeners\ExportEvent;

use Innoboxrr\LaravelOptions\Notifications\Option\ExportNotification;
use Innoboxrr\LaravelOptions\Http\Events\Option\Events\ExportEvent;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendExportNotification
{

    public function __construct()
    {
        //
    }

    public function handle(ExportEvent $event)
    {

        $event->user
            ->notify((new ExportNotification($event->data))->locale($event->locale));


    }

}