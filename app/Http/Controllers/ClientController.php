<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Client;
use App\Traits\HttpResponses;
use DateTime;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends Controller
{
    use HttpResponses;


    public function index(Request $request)
    {

        $params = $request->query(); //retorna os valores que vieram na query

        $clients = Client::query();

        if ($request->has('name') && !empty($params['name'])) {
            $clients->where('name', 'ilike', "%" . $params['name'] . "%");
        }

        if ($request->has('cpf') && !empty($params['cpf'])) {
            $clients->where('cpf', $params['cpf']);
        }

        if ($request->has('date_birth') && !empty($params['date_birth'])) {
            $clients->where('date_birth', $params['date_birth']);
        }

        return $clients->get();
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();

            $request->validate([
                'name' => 'string|required',
                'email' => 'email|required|unique:clients',
                'date_birth' => 'date_format:Y-m-d|required',
                'cpf' => 'string|required|unique:clients',
                'address' => 'string|required'
            ]);

            $client = Client::create($data);

            return $client;
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $data = $request->only('name', 'email', 'date_birth', 'address'); //vai atualizar somente esses campos

            $request->validate([
                'name' => 'string',
                'email' => 'email|unique:clients',
                'date_birth' => 'date_format:Y-m-d',
                'address' => 'string'
            ]);

            $client = Client::find($id);

            if(!$client) return $this->error('Cliente não encontrado', Response::HTTP_NOT_FOUND);

            $client->update($data);

            return $client;
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function destroy($id){
        $client = Client::find($id);

        if(!$client) return $this->error('Cliente não encontrado', Response::HTTP_NOT_FOUND);

        $client->delete();

        return $this->error('', Response::HTTP_NO_CONTENT);
    }


    public function getAwards(){
        $date =  (new DateTime('now'))->format('Y-m-d H:i');
        $awards = Award::query()->first();
        #$awards = Award::query()->whereBetween('date',  ["$date:00", "$date:59"])->get();

        //$clients = Client::query()->take(3)->inRandomOrder()->get();
        return $awards;
    }
}
