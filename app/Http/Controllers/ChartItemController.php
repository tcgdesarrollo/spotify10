<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChartItemRequest;
use App\Models\ChartItem;
use Illuminate\Http\Response;

class ChartItemController extends Controller
{



    public function index()
    {
        $query = ChartItem::all();
        return $this->sendResponse($query);
    }



    public function store(ChartItemRequest $request): Response
    {
        $chart_item = ChartItem::create($request->all());
        return $this->sendResponse($chart_item->fresh(), 201);
    }


    public function show(string $id): Response
    {
        $chart_item = ChartItem::findOrFail($id);
        return $this->sendResponse($chart_item->fresh());
    }


    public function update(ChartItemRequest $request, string $id): Response
    {
        $chart_item = ChartItem::findOrFail($id);
        $chart_item->update($request->all());
        return $this->sendResponse($chart_item->fresh());
    }


    public function destroy(string $id): Response
    {
        $chart_item = ChartItem::findOrFail($id);
        $chart_item->delete();
        return $this->sendResponse("ok", 204);
    }
}
