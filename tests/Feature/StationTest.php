<?php

namespace Tests\Feature;

use App\Models\Station;
use Tests\TestCase;

class StationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_station_store(): void
    {
        $obj = Station::factory()->make()->toArray();
        $response_store = $this->post('/api/station',$obj,$this->getHeaders());
        $response_store->assertStatus(201);
        $created = json_decode($response_store->content(), true)['data'];
        $response = $this->get("/api/station/".$created['id'],$this->getHeaders());
        $response->assertStatus(200);
    }

    public function test_station_list(): void
    {

        $response = $this->get('/api/station', $this->getHeaders());
        $response->assertStatus(200);
    }

    public function test_station_delete(): void
    {
        $station = Station::latest()->first();
        $response = $this->delete("/api/station/$station->id", [],$this->getHeaders());
        $response->assertStatus(204);
    }

    public function getHeaders(): array
    {
        $response = $this->post('/api/auth/login', [
           'email' => 'user1@hosc.com',
            'password' => 'Hosc*2023'
        ], [
            'Accept' => 'application/json'
        ]);
        $result = $response->content();
        $token = json_decode($result, true)['data'];
        return [
            'Authorization' => "Bearer $token",
            'Accept' => 'application/json'
        ];
    }
}
