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
     * Inserta las listas. Es idempotente: si ya existe la URL solo corrige el
     * nombre (no duplica); si no existe, la crea.
     */
    public function up(): void
    {
        $now = now();
        foreach ($this->charts as $chart) {
            $existing = DB::table('charts')->where('url', $chart['url'])->first();
            if ($existing) {
                if ($existing->name !== $chart['name']) {
                    DB::table('charts')->where('id', $existing->id)->update([
                        'name' => $chart['name'],
                        'updated_at' => $now,
                    ]);
                }
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
