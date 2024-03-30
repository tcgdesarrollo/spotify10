<?php

namespace Database\Seeders;

use App\Models\Station;
use Illuminate\Database\Seeder;

class EmisoraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stations = [
            [
                "title" => "Radio Bayamo",
                "subtitle" => "24 horas",
                "audio" => "https://icecast.teveo.cu/7hdNcTbM",
                "buyUrl" => "",
                "downloadUrl" => "",
                "downloadFilename" => "",
                "cover" => "https://www.radiobayamo.icrt.cu/wp-content/uploads/2022/09/RadioBayamoCabina.jpg"
            ], [
                "title" => "Radio Jiguaní",
                "subtitle" => "Horario: 7:00 AM -1:00 PM",
                "audio" => "https://icecast.teveo.cu/nkz3TCfR",
                "buyUrl" => "",
                "downloadUrl" => "",
                "downloadFilename" => "",
                "cover" => "https://www.radiobayamo.icrt.cu/wp-content/uploads/2020/01/Radio-Jiguaní.jpg"
            ], [
                "title" => "Radio Granma (Manzanillo)",
                "subtitle" => "Horario: 6:00 AM -12:00 AM",
                "audio" => "https://icecast.teveo.cu/9RLhkmRH",
                "buyUrl" => "",
                "downloadUrl" => "",
                "downloadFilename" => "",
                "cover" => "https://www.radiobayamo.icrt.cu/wp-content/uploads/2020/02/RGranma.jpg"
            ],
            [
                "title" => "Radio Ciudad Monumento (Bayamo)",
                "subtitle" => "Horario: 7:00 AM -1:00 PM",
                "audio" => "https://icecast.teveo.cu/KR43FF7C",
                "buyUrl" => "",
                "downloadUrl" => "",
                "downloadFilename" => "",
                "cover" => "https://www.radiobayamo.icrt.cu/wp-content/uploads/2020/02/RCM.jpg"
            ], [
                "title" => "Radio Sierra Maestra (Guisa)",
                "subtitle" => "Horario: 7:00 AM -1:00 PM",
                "audio" => "https://icecast.teveo.cu/wcdnsH3K",
                "buyUrl" => "",
                "downloadUrl" => "",
                "downloadFilename" => "",
                "cover" => "https://www.radiobayamo.icrt.cu/wp-content/uploads/2020/01/Radio-Sierra-Maestra.jpg"
            ], [
                "title" => "Radio Portada de la Libertad (Niquero)",
                "subtitle" => "Horario: 7:00 AM -12:00 AM",
                "audio" => "https://icecast.teveo.cu/mN9kqbNs",
                "buyUrl" => "",
                "downloadUrl" => "",
                "downloadFilename" => "",
                "cover" => "https://www.radiobayamo.icrt.cu/wp-content/uploads/2020/01/Portada-de-la-Libertad.jpg"
            ]
        ];

        foreach ($stations as $station) {
            Station::updateOrCreate(
                [
                    'audio' => $station['audio']
                ],
                $station
            );
        }

    }
}
