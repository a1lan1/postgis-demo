<?php

namespace App\Contracts;

interface DistanceServiceInterface
{
    public function calculate(float $lat1, float $lng1, float $lat2, float $lng2): float;
}
