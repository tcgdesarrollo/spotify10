<?php

namespace App\Console\Commands;

use App\Models\Chart;
use App\Models\ChartDate;
use App\Models\ChartItem;
use Illuminate\Console\Command;
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
        if (ChartDate::where([['date', $date], ['chart_id', $chart->id]])->exists())
            return false;
        else
            $chart_date = ChartDate::create(['date' => $date, 'chart_id' => $chart->id]);

        $elements->each(function (Crawler $node, $i) use ($chart_date) {
            $position = $node->filter(".c-label")->first()->text();
            $row = $node->filter(".o-chart-results-list-row");
            $title = $row->filter("li.lrv-u-width-100p ul li")->eq(0)->text();
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
                ]
            );
            $this->comment("$position. $title $last $peak $weeks");
        });

    }
}
