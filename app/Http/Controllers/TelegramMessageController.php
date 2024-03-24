<?php

namespace App\Http\Controllers;

use App\Http\Requests\TelegramMessageRequest;
use App\Models\TelegramMessage;
use Illuminate\Http\Response;

class TelegramMessageController extends Controller
{



    public function index()
    {
        $query = TelegramMessage::all();
        return $this->sendResponse($query);
    }



    public function store(TelegramMessageRequest $request): Response
    {
        $telegram_message = TelegramMessage::create($request->all());
        return $this->sendResponse($telegram_message->fresh(), 201);
    }


    public function show(string $id): Response
    {
        $telegram_message = TelegramMessage::findOrFail($id);
        return $this->sendResponse($telegram_message->fresh());
    }


    public function update(TelegramMessageRequest $request, string $id): Response
    {
        $telegram_message = TelegramMessage::findOrFail($id);
        $telegram_message->update($request->all());
        return $this->sendResponse($telegram_message->fresh());
    }


    public function destroy(string $id): Response
    {
        $telegram_message = TelegramMessage::findOrFail($id);
        $telegram_message->delete();
        return $this->sendResponse("ok", 204);
    }
}
