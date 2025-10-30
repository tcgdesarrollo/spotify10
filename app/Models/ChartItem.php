<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartItem extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];

//    protected $appends = ['fulltitle'];


    public function chart_date()
    {
        return $this->belongsTo(ChartDate::class);

    }

    public function getfulltitleAttribute()
    {
        $title = "$this->position. $this->title  $this->singer ($this->last_position $this->peak_position $this->week_on_chart)";
        $chart = $this->chart_date->chart;
        if (isset($this->streams) && str_contains($chart->name, 'Global Daily'))
            $title .= " (Streams: " . number_format($this->streams, 0, ',', '.') . ")";
        return $title;

    }
}
