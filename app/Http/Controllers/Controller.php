<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function sendResponse($result, $state = 200): Application|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        return response(['data' => $result], $state);
    }


    public function sendResponsePaginate($query, $state = 200, $page = 1, $total = 5): Application|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        return response(['data' => $query->paginate($total, page: $page)], $state);
    }
}
