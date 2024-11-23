<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('notify', function () {
    $this->info('SendTaskNotifications komandasini ishga tushiring:');
    Artisan::call('app:task-deadline-notification');
    $this->info('Bildirishnomalar muvaffaqiyatli yuborildi!');
});

