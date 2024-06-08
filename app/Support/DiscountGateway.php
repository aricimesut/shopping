<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

class DiscountGateway
{
    private $namespace = null;

    public function __construct($type)
    {
        $namespace = 'App\\Support\\Discount\\' . $type;
        $this->namespace = $namespace;
    }

    public function getClassName()
    {
        return $this->namespace;
    }

    public function getClass()
    {
        return new $this->namespace();
    }
}
