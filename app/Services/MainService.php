<?php

namespace App\Services;

use App\Traits\ApiResponser;
use Illuminate\Database\Eloquent\Model;

class MainService
{
    use ApiResponser;

    protected Model $model;

}
