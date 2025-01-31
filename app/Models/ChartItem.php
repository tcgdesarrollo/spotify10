<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartItem extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $appends = ['fulltitle'];

    public function getfulltitleAttribute()
    {
        $title = "$this->position. $this->title  $this->singer ($this->last_position $this->peak_position $this->week_on_chart)";
        if (isset($this->streams))
            $title.= " (Streams: ".number_format($this->streams, 0, ',', '.').")";
        return $title;

    }
}
