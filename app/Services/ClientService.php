<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Mockery\Exception;

class ClientService
{
    public static function addClient(array $validatedAddClient): bool
    {
        $client = Client::create($validatedAddClient);

        if (!is_object($client)) return false;
        return true;
    }

    public static function updateClient(array $validatedEditClient, int $id): bool
    {
        $client = Client::where('id', $id)->firstOrFail();
        $client->update($validatedEditClient);

        if (!is_object($client)) return false;
        return true;
    }

    public static function removeClient(int $id): void
    {
        $client = Client::where('id' , $id)->first();
        if(!$client){
         throw new Exception("Client with this id doesnot exist");
        } 
        $result = $client->delete();
    }
}
