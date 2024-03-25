<?php

namespace App\Console\Commands;

use App\Models\Chart;
use App\Models\ChartDate;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class ReadHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:read-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $charts = Chart::all();
        foreach ($charts as $chart) {
            $browser = new HttpBrowser(HttpClient::create());
            if (str_contains($chart->url, 'billboard')) {
                $raw_url = $chart->url;
                $cont = 100;
                while ($cont > 0) {
                    $first_date = ChartDate::where('chart_id', $chart->id)->orderBy('date')->first()->date;
                    $first_date_parse = Carbon::parse($first_date)->subDays(7)->format('Y-m-d');
                    $this->comment($first_date_parse);
                    $old_url = $raw_url . "$first_date_parse/";
                    $this->comment($old_url);
                    $browser->request('GET', $old_url);
                    $html = $browser->getResponse();
                    $crawler = new Crawler($html);
                    (new ReadChart())->parseBillboard($crawler, $chart);
                    $cont--;
                }

            } elseif (str_contains($chart->url, 'officialcharts')) {
                continue;
                (new ReadChart())->parseUk($crawler, $chart);
            }
        }
    }
}
