<?php

namespace App\Console\Commands;

use App\Http\Controllers\TelegramMessageController;
use App\Models\Chart;
use App\Models\ChartDate;
use App\Models\ChartItem;
use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
//        $charts = Chart::find([12]);
        $charts = Chart::all();
        foreach ($charts as $chart) {
            $browser = new HttpBrowser(HttpClient::create());
            $browser->request('GET', $chart->url);
            $html = $browser->getResponse();
            $crawler = new Crawler($html);
            if (str_contains($chart->url, 'billboard'))
                $this->parseBillboard($crawler, $chart);
            elseif (str_contains($chart->url, 'officialcharts')) {
                $this->parseUk($crawler, $chart);
            } elseif (str_contains($chart->url, 'pistacubana')) {
                $this->parsePistacubana($crawler, $chart);
            } elseif (str_contains($chart->url, 'spotify')) {
                $this->parseSpotify($crawler, $chart);
            } elseif (str_contains($chart->url, 'mediatraffic')) {
                $this->parseMediatraffic($crawler, $chart);
            }

        }
        return true;
    }

    public function parseBillboard($crawler, $chart): void
    {
        $elements = $crawler->filter('.o-chart-results-list-row-container');
        $date = $crawler->filter('.chart-results p.c-tagline')->first()->text();
        $date = str_replace('Week of ', "", $date);
        $date = $this->dateConverter($date, $chart->url);
        $chart_date = ChartDate::firstOrCreate(['date' => $date, 'chart_id' => $chart->id]);
        if ($chart_date->wasRecentlyCreated) {
            (new TelegramMessageController())->store("Agregada la lista $chart->name para la fecha $date");
        } else {
            return;
        }
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
            sleep(1);
//            $this->comment("$position. $title - $singer ($last $peak $weeks)");
        });
        $this->messagePositions($chart_date);

    }

    public function messagePositions($chart_date): void
    {
        $positions = ChartItem::where([['chart_date_id', $chart_date->id], ['position', '<=', 3]])->get();
        $chart_date = $chart_date->fresh();
        $message = $chart_date->fullname . " \n";
        foreach ($positions as $position) {
            $message .= $position->fulltitle . " \n";
        }
        (new TelegramMessageController())->store($message);
    }

    /**
     * Convierte la fecha a español
     * @param $fechaString
     * @param $chart_name
     * @return string
     */
    public function dateConverter($fechaString, $chart_name): string
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

    public function parseUk($crawler, $chart): void
    {
        $date = $crawler->filter('section.gutter')->filter('form')->filter('input')->attr('value');
        $chart_date = ChartDate::firstOrCreate(['date' => $date, 'chart_id' => $chart->id]);
        if ($chart_date->wasRecentlyCreated) {
            (new TelegramMessageController())->store("Agregada la lista $chart->name para la fecha $date. Solo para usuarios PRO");
        } else {
            return;
        }
        $elements = $crawler->filter('.chart-items');
        $elements->filter('.chart-item')->each(function ($node, $i) use ($chart_date) {
            $node->filter(".chart-item-content")->each(function ($subn) use ($chart_date) {
                $position = $subn->filter(".position")->filter("strong")->text();
                $image = $subn->filter(".chart-image")->filter("img")->attr("src");
                $title = $subn->filter(".description")->filter("p")->filter("a.chart-name")->filter('span')->last()->text();
                $singer = $subn->filter(".description")->filter("p")->filter("a.chart-artist")->text();
                $stats = $subn->filter(".description")->filter(".stats")->filter("ol");
                $last_week = $stats->filter("li")->eq(0)->text();
                if (str_contains(Str::lower($last_week), 'new')) {
                    $last_week = '-';
                } else {
                    $last_week = str_replace("LW: ", "", $last_week);
                    $last_week = str_replace(",", "", $last_week);
                }
                $peak = $stats->filter("li")->eq(1)->text();
                $peak = str_replace("Peak: ", "", $peak);
                $peak = str_replace(",", "", $peak);

                $weeks = $stats->filter("li")->eq(2)->text();
                $weeks = str_replace("Weeks: ", "", $weeks);
                ChartItem::updateOrCreate(
                    [
                        'chart_date_id' => $chart_date->id,
                        'position' => $position
                    ],
                    [
                        "title" => Str::title($title),
                        "last_position" => $last_week,
                        "peak_position" => $peak,
                        "week_on_chart" => $weeks,
                        "image" => $image,
                        "singer" => Str::title($singer)
                    ]
                );

            });

//            $this->comment($position->text());
        });
        $this->messagePositions($chart_date);
    }

    public function parsePistacubana($crawler, $chart): void
    {
        $elements = $crawler->filter('.side_post.trans_400');
        $date_full = $crawler->filter(".home_content")->filter('h3')->text();
        $date = explode("FECHA OFICIAL:", $date_full)[1];
        $date = explode("ORDENADO", $date)[0];
        $date = $this->dateConverterPistacubana($date);
        $this->comment($date);
        $chart_date = ChartDate::firstOrCreate(['date' => $date, 'chart_id' => $chart->id]);
        if ($chart_date->wasRecentlyCreated) {
            (new TelegramMessageController())->store("Agregada la lista $chart->name para la fecha $date");
        } else {
            return;
        }
        $elements->each(function ($node, $i) use ($chart_date) {
            if ($i > 0) {
                $position = $node->filter('.event_date')->filter('.event_day')->text();
                $title = $node->filter('.side_post_content')->filter('.side_post_title')->text();
                $singer = $node->filter('.side_post_content')->filter('.post_meta')->eq(1)->text();
                $image = "https://www.pistacubana.com/" . $node->filter('img')->attr('src');
//                $link = $node->filter("i")->filter(".fa.fa-chevron-down")->attr("onclick");
//                $this->comment($link);
//                $subdiv = $node->filter("[contains@id,'primary-')]")->text();
//                $this->comment($subdiv);
                ChartItem::updateOrCreate(
                    [
                        'chart_date_id' => $chart_date->id,
                        'position' => $position
                    ],
                    [
                        "title" => $title,
//                    "last_position" => $last,
//                    "peak_position" => $peak,
//                    "week_on_chart" => $weeks,
                        "image" => $image,
                        "singer" => $singer
                    ]
                );
            }
        });
        $this->messagePositions($chart_date);
    }

    /**
     * Convierte la fecha a español
     * @param $fechaString
     * @return string
     */
    public function dateConverterPistacubana($fechaString): string
    {
        $months = [
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre'
        ];
        $date_month = explode('/', $fechaString)[1];
        $real_month = array_search($date_month, $months);
        if ($real_month) {
            $fechaString = str_replace($date_month, $real_month + 1, $fechaString);
            $fechaObjeto = Carbon::parse($fechaString);
            // Imprimir la fecha en el formato deseado
            return $fechaObjeto->format('Y-m-d');
        }
        return $fechaString;
    }

    public function parseSpotify($crawler, $chart): void
    {
        Log::debug($crawler->text());
        $elements = $crawler->filter('.contentSpacing');
        $this->comment($elements->text());
        $date = $crawler->filter('.chart-results p.c-tagline')->first()->text();
        $date = str_replace('Week of ', "", $date);
        $date = $this->dateConverter($date, $chart->url);
        $chart_date = ChartDate::firstOrCreate(['date' => $date, 'chart_id' => $chart->id]);
        if ($chart_date->wasRecentlyCreated) {
            (new TelegramMessageController())->store("Agregada la lista $chart->name para la fecha $date");
        } else {
            return;
        }
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
            sleep(1);
//            $this->comment("$position. $title - $singer ($last $peak $weeks)");
        });
        $this->messagePositions($chart_date);

    }

    public function parseMediatraffic($crawler, $chart): void
    {
        $elements = $crawler->filter('#AutoNumber3');
        $subelements = $elements->filter('tr')->filter("td")->filter('tr')->filter('td');
        //esta es cada una de las posiciones
        $subelements->each(function ($node, $i) {
            Log::debug($node->text());
            $row = $node;
            $this->comment($row->text());
            $pos = $row->image()->attr('src');
            $this->comment($pos);
        });
        return;
        $date = str_replace('Week of ', "", $date);
        $date = $this->dateConverter($date, $chart->url);
        $chart_date = ChartDate::firstOrCreate(['date' => $date, 'chart_id' => $chart->id]);
        if ($chart_date->wasRecentlyCreated) {
            (new TelegramMessageController())->store("Agregada la lista $chart->name para la fecha $date");
        } else {
            return;
        }
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
            sleep(1);
//            $this->comment("$position. $title - $singer ($last $peak $weeks)");
        });
        $this->messagePositions($chart_date);

    }

}
