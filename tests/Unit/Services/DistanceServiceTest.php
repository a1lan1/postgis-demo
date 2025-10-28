<?php

namespace Tests\Unit\Services;

use App\Services\DistanceService;

beforeEach(function () {
    $this->distanceService = new DistanceService;
});

it('calculates the distance between two points correctly', function () {
    $lat1 = 51.5074;
    $lng1 = -0.1278;
    $lat2 = 48.8566;
    $lng2 = 2.3522;
    $expectedDistance = 343.5;

    $actualDistance = $this->distanceService->calculate($lat1, $lng1, $lat2, $lng2);

    expect($actualDistance)->toEqualWithDelta($expectedDistance, 1.0);
});
