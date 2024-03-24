<?php

namespace Tests\Feature;

use App\Models\AppVersion;
use Tests\TestCase;

class AppVersionTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_appversion_store(): void
    {
        $obj = AppVersion::factory()->make()->toArray();
        $response_store = $this->post('/api/app-version',$obj,$this->getHeaders());
        $response_store->assertStatus(201);
        $created = json_decode($response_store->content(), true)['data'];
        $response = $this->get("/api/app-version/".$created['id'],$this->getHeaders());
        $response->assertStatus(200);
    }

    public function test_appversion_list(): void
    {

        $response = $this->get('/api/app-version', $this->getHeaders());
        $response->assertStatus(200);
    }

    public function test_appversion_delete(): void
    {
        $appversion = AppVersion::latest()->first();
        $response = $this->delete("/api/app-version/$appversion->id", [],$this->getHeaders());
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
