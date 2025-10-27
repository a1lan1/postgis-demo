<?php

namespace App\Services;

use App\Contracts\DistanceServiceInterface;
use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Database\PostgisFunctions\ST;
use Illuminate\Support\Facades\DB;

class DistanceService implements DistanceServiceInterface
{
    /**
     * Calculate the distance between two points.
     */
    public function calculate(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $point1 = Point::makeGeodetic($lat1, $lng1);
        $point2 = Point::makeGeodetic($lat2, $lng2);

        // Create an expression to calculate the distance in meters
        $distanceInMetersExpression = ST::distanceSphere($point1, $point2);

        // Execute a database query to evaluate the expression
        $distanceInMeters = DB::query()
            ->select($distanceInMetersExpression->as('distance'))
            ->value('distance');

        // Convert to kilometers
        return $distanceInMeters / 1000;
    }
}
