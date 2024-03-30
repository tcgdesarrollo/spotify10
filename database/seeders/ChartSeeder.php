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
            ['name' => 'Billboard Latin Songs', 'url' => 'https://www.billboard.com/charts/latin-songs/'],
            ["name" => "Billboard 200", "url" => "https://www.billboard.com/charts/billboard-200/"],
            ["name" => "Billboard Hot Dance/Electronic Songs", "url" => "https://www.billboard.com/charts/dance-electronic-songs/"],
            ["name" => "Billboard Global 200", "url" => "https://www.billboard.com/charts/billboard-global-200/"],
            ["name" => "Billboard Radio Songs", "url" => "https://www.billboard.com/charts/radio-songs/"],
            ["name" => "Billboard Artist 100", "url" => "https://www.billboard.com/charts/artist-100/"],
            ["name" => "UK Official Singles Chart", "url" => "https://www.officialcharts.com/charts/uk-top-40-singles-chart/"],
            ["name" => "UK Official Albums Chart Top 100", "url" => "https://www.officialcharts.com/charts/albums-chart/"],
            ['name' => 'Pistacubana Top 100', 'url' => 'https://www.pistacubana.com/lista/top100/172024/posicion']
        ];
        foreach ($lists as $item) {
            Chart::updateOrCreate(
                ['name' => $item['name']],
                ['url' => $item['url']]
            );
        }
    }
}
