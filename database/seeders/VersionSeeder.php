<?php

namespace Database\Seeders;

use App\Models\AppVersion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppVersion::updateOrCreate(
            [
                'version' => '0.1'
            ],
            [
                'changes' => "Versión beta"
            ]
        );
    }
}
