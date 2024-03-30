<?php

namespace App\Http\Controllers;

use App\Http\Requests\StationRequest;
use App\Models\Station;
use Illuminate\Http\Response;

class StationController extends Controller
{



    public function index()
    {
        $query = Station::all();
        return $this->sendResponse($query);
    }



    public function store(StationRequest $request): Response
    {
        $station = Station::create($request->all());
        return $this->sendResponse($station->fresh(), 201);
    }


    public function show(string $id): Response
    {
        $station = Station::findOrFail($id);
        return $this->sendResponse($station->fresh());
    }


    public function update(StationRequest $request, string $id): Response
    {
        $station = Station::findOrFail($id);
        $station->update($request->all());
        return $this->sendResponse($station->fresh());
    }


    public function destroy(string $id): Response
    {
        $station = Station::findOrFail($id);
        $station->delete();
        return $this->sendResponse("ok", 204);
    }
}
