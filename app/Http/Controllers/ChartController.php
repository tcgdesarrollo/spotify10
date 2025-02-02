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
        $type = $request->type;
        if (isset($type))
            $query = Chart::where('name', 'like', "%$type%")->get();
        else
            $query = Chart::orderBy('name')->get();
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
        $chart = Chart::findOrFail($id);
        $me = auth()->user();
        if ($me->isActive || $me->role_id == 1)
            $dates = $chart->dates()->latest()->limit(5)->get()->load('items');
        else
            $dates = $chart->dates()->latest()->limit(2)->get()->load('items');
        return $this->sendResponse(['chart' => $chart, 'dates' => $dates]);
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
