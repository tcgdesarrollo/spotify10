<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Listas que deben existir en el servidor.
     */
    private array $charts = [
        ['name' => 'Beatport Top 100', 'url' => 'https://www.beatport.com/es/top-100'],
        ['name' => 'MediaTraffic Global Track Top 40', 'url' => 'http://www.mediatraffic.de/tracks.htm'],
    ];

    /**
     * Inserta las listas. Es idempotente: solo crea las que no existan ya
     * (comparando por URL), para no duplicar registros previos.
     */
    public function up(): void
    {
        $now = now();
        foreach ($this->charts as $chart) {
            if (DB::table('charts')->where('url', $chart['url'])->exists()) {
                continue;
            }
            DB::table('charts')->insert([
                'name' => $chart['name'],
                'url' => $chart['url'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Solo se elimina Beatport, que es la lista realmente nueva. MediaTraffic
     * pudo existir antes de esta migración (seeder), así que no se toca para no
     * borrar en cascada sus fechas/canciones históricas.
     */
    public function down(): void
    {
        DB::table('charts')->where('url', 'https://www.beatport.com/es/top-100')->delete();
    }
};
