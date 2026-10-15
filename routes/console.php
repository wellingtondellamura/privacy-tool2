<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Privacy Policy, section 6: error and failure records are kept for up to 90 days.
Schedule::command('telescope:prune --hours=2160')->daily();
