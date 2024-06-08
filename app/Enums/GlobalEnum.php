<?php

namespace App\Enums;

class GlobalEnum
{

    /**
     * @return array
     */
    public static function getConstants(): array
    {
        $oClass = new \ReflectionClass(static::class);
        return $oClass->getConstants();
    }
}
