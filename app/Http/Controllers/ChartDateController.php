<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChartDateRequest;
use App\Models\ChartDate;
use Illuminate\Http\Response;

class ChartDateController extends Controller
{



    public function index()
    {
        $query = ChartDate::all();
        return $this->sendResponse($query);
    }



    public function store(ChartDateRequest $request): Response
    {
        $chart_date = ChartDate::create($request->all());
        return $this->sendResponse($chart_date->fresh(), 201);
    }


    public function show(string $id): Response
    {
        $chart_date = ChartDate::findOrFail($id);
        return $this->sendResponse($chart_date->fresh());
    }


    public function update(ChartDateRequest $request, string $id): Response
    {
        $chart_date = ChartDate::findOrFail($id);
        $chart_date->update($request->all());
        return $this->sendResponse($chart_date->fresh());
    }


    public function destroy(string $id): Response
    {
        $chart_date = ChartDate::findOrFail($id);
        $chart_date->delete();
        return $this->sendResponse("ok", 204);
    }
}
