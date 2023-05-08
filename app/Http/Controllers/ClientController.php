<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Client;
use Mockery\Exception;
use App\Services\ClientService;
use App\Http\Requests\AddClientRequest;
use App\Http\Requests\EditClientRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ClientResource;
use Illuminate\Http\Response;

class ClientController extends Controller
{
    public function viewAllClients()
    {
        $clients = Client::all();

        return response()->json([
            'total' => count($clients),
            'clients' => ClientResource::collection($clients)
        ], 200);
    }

    public function addClient(AddClientRequest $request): JsonResponse
    {
        $validatedAddClient = $request->validated();
        $result = ClientService::addClient($validatedAddClient);
        if (!$result) {
            return response()->json([
                'message' => 'Could not add client'
            ], 400);
        }
        return response()->json([
            'message' => 'Client added successfully'
        ]);
    }

    public function updateClient(EditClientRequest $request, int $id): JsonResponse
    {
        $validatedEditClient = $request->validated();
        $result = ClientService::updateClient($validatedEditClient, $id);
        if (!$result) {
            return response()->json([
                'message' => 'Could not update client'
            ], 400);
        }
        return response()->json([
            'message' => 'Client updated successfully'
        ]);
    }

    public function deleteClient(int $id): JsonResponse
    {
        try {
            ClientService::removeClient($id);
            return response()->json([
                'message' => 'Client deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

    }

    public function updateClientStatus(Request $request)
    {
        $client = Client::where('id', $request->id)->first();
        try {
            if (!$client->status) {
                $client->status = true;
            } else {
                $client->status = false;
            }
            $client->save();
            $result = [
                'status' => 200,
                'message' => 'client status updated',
                'client' => $client,
            ];
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
        return response()->json($result, $result['status']);
    }


}
