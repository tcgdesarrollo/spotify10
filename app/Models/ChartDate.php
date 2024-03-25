<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChartDate extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];


    public function items()
    {
        return $this->hasMany(ChartItem::class)->take(40);

    }

}
