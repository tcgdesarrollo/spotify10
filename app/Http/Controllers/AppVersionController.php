<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppVersionRequest;
use App\Models\AppVersion;
use Illuminate\Http\Response;

class AppVersionController extends Controller
{


    public function index()
    {
        $query = AppVersion::latest()->first();
        return $this->sendResponse($query);
    }


    public function store(AppVersionRequest $request): Response
    {
        AppVersion::where('id', '>=', 1)->delete();
        $app_version = AppVersion::create($request->all());
        return $this->sendResponse($app_version->fresh(), 201);
    }


    public function show(string $id): Response
    {
        $app_version = AppVersion::findOrFail($id);
        return $this->sendResponse($app_version->fresh());
    }


    public function update(AppVersionRequest $request, string $id): Response
    {
        $app_version = AppVersion::findOrFail($id);
        $app_version->update($request->all());
        return $this->sendResponse($app_version->fresh());
    }


    public function destroy(string $id): Response
    {
        $app_version = AppVersion::findOrFail($id);
        $app_version->delete();
        return $this->sendResponse("ok", 204);
    }
}
