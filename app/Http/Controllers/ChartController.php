<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChartRequest;
use App\Models\Chart;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChartController extends Controller
{

    const relations = ['dates.items'];

    public function index(Request $request): Application|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        $request->validate([
            'type' => 'nullable|max:100'
        ]);
        $type = $request->type ?? 'billboard';
        $me = auth()->user();
        if (isset($me))
            $query = Chart::all();
        else
            $query = Chart::where('url', 'like', "%$type%")->get();
        return $this->sendResponse($query);
    }


    public function store(ChartRequest $request): Application|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        $chart = Chart::updateOrCreate(
            ['url' => $request->url],
            ['name' => $request->name]
        );
        return $this->sendResponse($chart->fresh(), 201);
    }


    public function show(string $id): Response
    {
        $chart = Chart::with(self::relations)->findOrFail($id);
        return $this->sendResponse($chart);
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
