<?php

namespace App\Console\Commands;

use App\Mail\SendAwardToClient;
use App\Models\Award;
use App\Models\Client;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAwards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-awards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia email de prêmios para clientes';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $date =  (new DateTime('now'))->format('Y-m-d H:i'); //Retorna a hora atual - '2023-12-14 09:56
        $awards = Award::query()->whereBetween('date', ["$date:00", "$date:59"])->get(); //Retornando os prêmios por minuto

        foreach($awards as $award) {
            $clients = Client::query()->take($award->amount)->inRandomOrder()->get(); //Interagindo com a tabela de clientes e pegando o qntd de cupom

            Log::info($award->amount);

            foreach($clients as $client) {
                Log::info("Enviando email para $client"); //Debugando o código
                Mail::to($client->email, $client->name)
                ->send(new SendAwardToClient($client, $award));
            }
        }
    }
}
