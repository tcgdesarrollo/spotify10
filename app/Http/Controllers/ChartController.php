<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChartRequest;
use App\Models\Chart;
use App\Models\ChartItem;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class ChartController extends Controller
{

    const relations = ['dates.items'];

    public function index(Request $request): Application|Response|\Illuminate\Contracts\Foundation\Application|ResponseFactory
    {
        $request->validate([
            'type' => 'nullable|max:100'
        ]);
        $type = $request->type;
        // ponytail: TTL cache, no active invalidation. New charts show within 6h; Cache::forget('charts_index_*') in the scraper if instant refresh needed.
        $query = Cache::remember('charts_index_' . ($type ?? 'all'), now()->addHours(6), function () use ($type) {
            if (isset($type))
                return Chart::where('name', 'like', "%$type%")->get();
            return Chart::orderBy('name')->get();
        });
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


    public function show(Request $request, string $id): Response
    {
        $me = auth()->user();
        $limit = ($me->isActive || $me->role_id == 1) ? 5 : 2;
        // ponytail: TTL cache keyed by chart + tier (2 vs 5 dates). Stale up to 6h; forget "chart_show_{$id}_*" in the scraper for instant refresh.
        // Solo metadatos de fechas: los items van aparte, uno por fecha, que es lo unico que muestra el front.
        $data = Cache::remember("chart_show_{$id}_{$limit}", now()->addHours(6), function () use ($id, $limit) {
            $chart = Chart::findOrFail($id);
            $dates = $chart->dates()->latest()->limit($limit)->get(['id', 'chart_id', 'date']);
            return ['chart' => $chart, 'dates' => $dates];
        });

        $requested = $request->query('date');
        $date_id = $requested ?: $data['dates']->first()?->id;

        // El limite de fechas es el muro de pago: no servir una fecha fuera de las permitidas.
        if ($date_id !== null && !$data['dates']->contains('id', $date_id))
            abort(403);

        // Una fecha ya publicada no cambia nunca, se puede cachear largo.
        $items = $date_id === null ? [] : Cache::remember(
            "chart_date_items_$date_id",
            now()->addDays(30),
            fn() => ChartItem::where('chart_date_id', $date_id)->orderBy('position')->get()
        );

        if ($requested)
            return $this->sendResponse(['items' => $items]);

        return $this->sendResponse($data + ['items' => $items]);
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
