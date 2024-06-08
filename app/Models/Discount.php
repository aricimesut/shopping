<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = ['category_id', 'reason', 'threshold', 'discount', 'type'];

}
