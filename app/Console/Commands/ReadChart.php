<?php

namespace App\Console\Commands;

use App\Http\Controllers\TelegramMessageController;
use App\Models\Chart;
use App\Models\ChartDate;
use App\Models\ChartItem;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class ReadChart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:read-chart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        $charts = Chart::all();
        foreach ($charts as $chart) {
            $browser = new HttpBrowser(HttpClient::create());
            $browser->request('GET', $chart->url);
            $html = $browser->getResponse();
            $crawler = new Crawler($html);
            if (str_contains($chart->url, 'billboard'))
                $this->parseBillboard($crawler, $chart);
        }
        return true;
    }

    private function parseBillboard($crawler, $chart)
    {
        $elements = $crawler->filter('.o-chart-results-list-row-container');
        $date = $crawler->filter('.chart-results p.c-tagline')->first()->text();
        $date = str_replace('Week of ', "", $date);
        $date = $this->dateConverter($date, $chart->url);
        $chart_date = ChartDate::firstOrCreate(['date' => $date, 'chart_id' => $chart->id]);
        if ($chart_date->wasRecentlyCreated) {
            (new TelegramMessageController())->store("Agregada la lista $chart->name para la fecha $date");
        } else return false;
        $elements->each(function (Crawler $node, $i) use ($chart_date) {
            $position = $node->filter(".c-label")->first()->text();
            $row = $node->filter(".o-chart-results-list-row");
            $image = $row->filter("li")->filter(".c-lazy-image .lrv-a-crop-1x1")->filter("img")->attr('data-lazy-src');
            $title = $row->filter("li.lrv-u-width-100p ul li h3")->eq(0)->text();
            $singer = $row->filter("li.lrv-u-width-100p ul li span")->eq(0)->text();
            $last = $row->filter("li.lrv-u-width-100p ul li")->eq(3)->text();
            $peak = $row->filter("li.lrv-u-width-100p ul li")->eq(4)->text();
            $weeks = $row->filter("li.lrv-u-width-100p ul li")->eq(5)->text();
            ChartItem::updateOrCreate(
                [
                    'chart_date_id' => $chart_date->id,
                    'position' => $position
                ],
                [
                    "title" => $title,
                    "last_position" => $last,
                    "peak_position" => $peak,
                    "week_on_chart" => $weeks,
                    "image" => $image,
                    "singer" => $singer
                ]
            );
            $this->comment("$position. $title - $singer ($last $peak $weeks)");
        });

    }

    /**
     * Convierte la fecha a español
     * @param $fechaString
     * @param $chart_name
     * @return string
     */
    private function dateConverter($fechaString, $chart_name): string
    {
        if (str_contains($chart_name, 'billboard')) {
            $fechaObjeto = DateTime::createFromFormat('F d, Y', $fechaString);
            // Verificar si la conversión fue exitosa
            if ($fechaObjeto instanceof DateTime) {
                // Imprimir la fecha en el formato deseado
                return $fechaObjeto->format('Y-m-d');
            }
        }
        return $fechaString;


    }
}
