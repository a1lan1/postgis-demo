<?php

namespace Tests\Feature\Api;

use App\Contracts\DistanceServiceInterface;

it('calculates the distance between two points', function () {
    $this->mock(DistanceServiceInterface::class)
        ->shouldReceive('calculate')
        ->once()
        ->with(51.5074, -0.1278, 48.8566, 2.3522)
        ->andReturn(343.5);

    $this->getJson('/distance?lat1=51.5074&lng1=-0.1278&lat2=48.8566&lng2=2.3522')
        ->assertOk()
        ->assertJson([
            'distance' => 343.5,
            'unit' => 'km',
        ]);
});

it('returns validation errors when calculating distance with invalid data', function (string $queryString, array|string $expectedErrors) {
    $this->getJson("/distance?$queryString")
        ->assertStatus(422)
        ->assertJsonValidationErrors($expectedErrors);
})->with([
    'missing lat1' => ['queryString' => 'lng1=-0.1&lat2=48.8&lng2=2.3', 'expectedErrors' => 'lat1'],
    'missing lng1' => ['queryString' => 'lat1=51.5&lat2=48.8&lng2=2.3', 'expectedErrors' => 'lng1'],
    'missing lat2' => ['queryString' => 'lat1=51.5&lng1=-0.1&lng2=2.3', 'expectedErrors' => 'lat2'],
    'missing lng2' => ['queryString' => 'lat1=51.5&lng1=-0.1&lat2=48.8', 'expectedErrors' => 'lng2'],
    'invalid lat1' => ['queryString' => 'lat1=abc&lng1=-0.1&lat2=48.8&lng2=2.3', 'expectedErrors' => 'lat1'],
    'invalid lng1' => ['queryString' => 'lat1=51.5&lng1=abc&lat2=48.8&lng2=2.3', 'expectedErrors' => 'lng1'],
    'invalid lat2' => ['queryString' => 'lat1=51.5&lng1=-0.1&lat2=abc&lng2=2.3', 'expectedErrors' => 'lat2'],
    'invalid lng2' => ['queryString' => 'lat1=51.5&lng1=-0.1&lat2=48.8&lng2=abc', 'expectedErrors' => 'lng2'],
]);
