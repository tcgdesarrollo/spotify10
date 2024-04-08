<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartDate extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $appends = ['fullname'];


    public function items()
    {
        return $this->hasMany(ChartItem::class)->orderBy('position');

    }

    public function chart()
    {
        return $this->belongsTo(Chart::class);

    }

    public function getfullnameAttribute()
    {
        $name = $this->chart()->first()->name;
        $name .= " Fecha: " . $this->attributes['date'];
        return $name;
    }

}
