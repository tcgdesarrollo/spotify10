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
        return $this->attributes['position']. '. '. $this->attributes['title'] . ' - ' . $this->attributes['singer'];

    }
}
