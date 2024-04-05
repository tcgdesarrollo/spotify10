<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Chart extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];


    public function dates()
    {
        return $this->hasMany(ChartDate::class)->orderByDesc('date')->where('date','>', Carbon::now()->subYears());

    }
}
