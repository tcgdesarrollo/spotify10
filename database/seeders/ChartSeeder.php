<?php

namespace Database\Seeders;

use App\Models\Chart;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $lists = [
            ['name' => 'Billboard Hot 100', 'url' => 'https://www.billboard.com/charts/hot-100/'],
            ['name' => 'Billboard Latin Songs', 'url' => 'https://www.billboard.com/charts/latin-songs/']
        ];
        foreach ($lists as $item) {
            Chart::updateOrCreate(
                ['name' => $item['name']],
                ['url' => $item['url']]
            );
        }
    }
}
