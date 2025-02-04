<?php

namespace App\Console\Commands;

use App\Models\AppVersion;
use App\Models\User;
use Illuminate\Console\Command;

class Tools extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:tools';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'role_id' => 1,
            ]
        );

    AppVersion::create([
        'version'=> '2.0',
        'changes'=>"Agrega autenticación"
    ]);
    }
}
