<?php

namespace App\Http\Controllers;

use App\Http\Requests\QueueEmailRequest;
use App\Models\QueueEmail;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use OpenApi\Annotations as OA;

class QueueEmailController extends Controller
{


    /**
     * @OA\Get(
     *     path="/api/queue-email",
     *     tags={"QueueEmail"},
     *     summary="List of queueemails",
     *     description="Get list of all registered queueemails",
     *     operationId="listQueueEmail",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *          @OA\JsonContent()
     *     ),
     *       security={{ "apiAuth": {} }}
     *     ),
     */
    public function index()
    {
        $query = QueueEmail::all();
        return $this->sendResponse($query);
    }


    public function store(string $type,int $user_id, array $to = ['aarzuagat@gmail.com'], array $extra = null): bool
    {
        if (gettype($extra) == 'array') {
            $extra = json_encode($extra);
        }
        QueueEmail::create([
            'type' => $type,
            'to' => json_encode($to),
            'language' => "es",
            'user_id' => $user_id,
            'extra' => $extra ?? null
        ]);
        if (env('APP_ENV') == 'local')
            Artisan::call('app:queue-email');
        return true;
    }


    /**
     * Show specific resource.
     *
     * @param string $id
     * @return Response
     *
     * @OA\get(
     * path="/api/queue-email/{queueemail}",
     *  @OA\Parameter(description="ID of queueemail", in="path", name="queueemail", required=true,
     *      @OA\Schema(type="integer", format="int64", example="1")
     *  ),
     * summary="Show specific queueemails",
     * operationId="getQueueEmail",
     * tags={"QueueEmail"},
     * security={{ "apiAuth": {} }},
     * @OA\Response(response=200, description="Operation executed successfully", @OA\JsonContent()),
     * @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     * @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     * )
     */
    public function show(string $id): Response
    {
        $queue_email = QueueEmail::findOrFail($id);
        return $this->sendResponse($queue_email->fresh());
    }


    /**
     * Update a resource.
     *
     * @param QueueEmailRequest $request
     * @param string $id
     * @return Response
     *
     * @OA\Put(
     * path="/api/queue-email/{queueemail}",
     * @OA\Parameter(description="ID of queueemail", in="path", name="queueemail", required=true,
     *      @OA\Schema(type="integer", format="int64", example="1")
     *  ),
     * summary="Update a queueemail",
     * operationId="updateQueueEmail",
     * tags={"QueueEmail"},
     * security={{ "apiAuth": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="data to update a queueemail",
     *    @OA\JsonContent(
     *       @OA\Property(property="name", type="string", example="Example"),
     *    ),
     * ),
     * @OA\Response(response=200, description="Operation executed successfully", @OA\JsonContent()),
     * @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     * @OA\Response(response=422, description="Bad request", @OA\JsonContent()),
     * @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     * )
     */
    public function update(QueueEmailRequest $request, string $id): Response
    {
        $queue_email = QueueEmail::findOrFail($id);
        $queue_email->update($request->all());
        return $this->sendResponse($queue_email->fresh());
    }

    /**
     * Delete a resource.
     *
     * @param string $id
     * @return Response
     * @OA\Delete(
     * path="/api/queue-email/{queueemail}",
     *  @OA\Parameter(description="ID of queueemail", in="path", name="queueemail", required=true,
     *      @OA\Schema(type="integer", format="int64", example="1")
     *  ),
     * summary="Delete a queueemail",
     * operationId="deleteQueueEmail",
     * tags={"QueueEmail"},
     * security={{ "apiAuth": {} }},
     * @OA\Response(response=200, description="Operation executed successfully", @OA\JsonContent()),
     * @OA\Response(response=204, description="Operation executed successfully", @OA\JsonContent()),
     * @OA\Response(response=400, description="Bad request", @OA\JsonContent()),
     * @OA\Response(response=404, description="Resource Not Found", @OA\JsonContent()),
     * )
     */
    public function destroy(string $id): Response
    {
        $queue_email = QueueEmail::findOrFail($id);
        $queue_email->delete();
        return $this->sendResponse("ok", 204);
    }
}
