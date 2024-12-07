<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;
use TelegramBot\Api\InvalidArgumentException;

class TelegramController extends Controller
{
    /**
     * @throws \Exception
     */
    public function sendMessage($message, $format = 'html')
    {
        $bot = new BotApi(env('BOT_TOKEN'));

        if (!isset($chat_id))
            $chat_id = env('TELEGRAM_CHAT_ID');
        $parts = str_split($message, 4095);
        foreach ($parts as $subm) {
            if (str_contains($subm, '_') || str_contains($subm, '*') || str_contains($subm, '&'))
                $subm = str_replace(['_', '*', '&'], '', $subm);
            try {
                $bot->sendMessage($chat_id, $subm, $format);
                sleep(3);
                return true;
            } catch (InvalidArgumentException|Exception $e) {
                Log::debug("error de telegram " . $e->getMessage(). " al enviar el mensaje $subm");
                $message = "Arzuaga revisa el sender de telegram actualizado";
            }
        }
        sleep(5);
        return false;
    }
}
