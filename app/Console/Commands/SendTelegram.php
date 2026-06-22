<?php

namespace App\Console\Commands;

use App\Http\Controllers\TelegramController;
use App\Models\TelegramMessage;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:telegram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle(): int
    {
        $messages = TelegramMessage::take(20)->get();
        foreach ($messages as $message) {
            $msg = "$message->description";
            if (env('APP_ENV') != 'prod')
                $msg = "(PRUEBAS): ". $msg;
            // Se reclama el mensaje antes de enviarlo para evitar que una
            // ejecución concurrente lo tome y se envíe dos veces.
            $message->delete();
            (new TelegramController())->sendMessage($msg,'html');
            sleep(1);
        }

        return true;
    }
}
