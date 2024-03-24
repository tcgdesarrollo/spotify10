<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChartRequest;
use App\Models\Chart;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;

class ChartController extends Controller
{


    public function index(): Application|Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $query = Chart::all();
        return $this->sendResponse($query);
    }


    public function store(ChartRequest $request): Response
    {
        $chart = Chart::create($request->all());
        return $this->sendResponse($chart->fresh(), 201);
    }


    public function show(string $id): Response
    {
        $chart = Chart::findOrFail($id);
        return $this->sendResponse($chart->fresh());
    }


    public function update(ChartRequest $request, string $id): Response
    {
        $chart = Chart::findOrFail($id);
        $chart->update($request->all());
        return $this->sendResponse($chart->fresh());
    }

    public function destroy(string $id): Response
    {
        $chart = Chart::findOrFail($id);
        $chart->delete();
        return $this->sendResponse("ok", 204);
    }
}
