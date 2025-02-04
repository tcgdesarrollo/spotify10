<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/


Schedule::command('app:read-chart')->cron('*/30 * * * *');
Schedule::command('send:telegram')->cron('* * * * *');
Schedule::command('run:tools')->cron('0 * * * *');
