<?php

namespace Tests\Feature;

use App\Models\TelegramMessage;
use Tests\TestCase;

class TelegramMessageTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_telegrammessage_store(): void
    {
        $obj = TelegramMessage::factory()->make()->toArray();
        $response_store = $this->post('/api/telegram-message',$obj,$this->getHeaders());
        $response_store->assertStatus(201);
        $created = json_decode($response_store->content(), true)['data'];
        $response = $this->get("/api/telegram-message/".$created['id'],$this->getHeaders());
        $response->assertStatus(200);
    }

    public function test_telegrammessage_list(): void
    {

        $response = $this->get('/api/telegram-message', $this->getHeaders());
        $response->assertStatus(200);
    }

    public function test_telegrammessage_delete(): void
    {
        $telegrammessage = TelegramMessage::latest()->first();
        $response = $this->delete("/api/telegram-message/$telegrammessage->id", [],$this->getHeaders());
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
