<?php

namespace App\Http\Controllers;

use App\Http\Requests\TelegramMessageRequest;
use App\Models\TelegramMessage;
use Illuminate\Support\Facades\Artisan;

class TelegramMessageController extends Controller
{
    public function index()
    {
        $telegrammessages = TelegramMessage::latest()->get();

        return response(['data' => $telegrammessages], 200);
    }

    public function store($message, $priority = 1)
    {

        TelegramMessage::updateOrCreate([
            'description' => $message,
            'priority' => $priority
        ]);
//        if (env('APP_ENV') != 'prod') {
//            Artisan::call('send:telegram');
//        }
        return true;

    }

    public function show($id)
    {
        $telegrammessage = TelegramMessage::findOrFail($id);

        return response(['data' => $telegrammessage], 200);
    }

    public function update(TelegramMessageRequest $request, $id)
    {
        $telegrammessage = TelegramMessage::findOrFail($id);
        $telegrammessage->update($request->all());

        return response(['data' => $telegrammessage], 200);
    }

    public function destroy($id)
    {
        TelegramMessage::destroy($id);

        return response(['data' => null], 204);
    }
}
