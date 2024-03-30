<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['created_at','updated_at'];


}
