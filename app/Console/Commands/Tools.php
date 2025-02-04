<?php

namespace App\Console\Commands;

use App\Models\AppVersion;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

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


        AppVersion::updateOrCreate([
            'version' => '2.0',
            'changes' => "Agrega autenticación"
        ]);
        User::updateOrCreate(
            ['email' => 'aarzuagat@gmail.com'],
            [
                'name' => 'Alberto',
                'password' => Hash::make('601-Daddy'),
                'role_id' => 1,
            ]
        );
    }
}
