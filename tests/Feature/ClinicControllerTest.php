<?php

use App\Contracts\ClinicServiceInterface;
use App\Models\Clinic;
use Inertia\Testing\AssertableInertia as Assert;

it('returns an empty list of clinics when no coordinates are provided', function () {
    $this->get(route('clinics.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('clinic/Index')
            ->has('clinics', 0)
        );
});

it('returns nearby clinics when coordinates are provided', function () {
    $clinics = Clinic::factory()->count(5)->create();

    $this->mock(ClinicServiceInterface::class)
        ->shouldReceive('getNearbyClinics')
        ->once()
        ->with(51.5074, -0.1278, 5000)
        ->andReturn($clinics);

    $this->get(route('clinics.index', ['lat' => 51.5074, 'lng' => -0.1278, 'radius' => 5000]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('clinic/Index')
            ->has('clinics', 5)
        );
});

it('creates a new clinic with valid data', function () {
    $clinicData = [
        'name' => 'St. James Clinic',
        'location' => [
            'type' => 'Point',
            'coordinates' => [-0.1278, 51.5074],
        ],
    ];

    $this->postJson(route('clinics.store'), $clinicData)
        ->assertStatus(201)
        ->assertJsonFragment(['name' => 'St. James Clinic']);

    $this->assertDatabaseHas('clinics', [
        'name' => 'St. James Clinic',
    ]);
});

it('returns validation errors when creating a clinic with invalid data', function (array $invalidData, array|string $expectedErrors) {
    $this->postJson(route('clinics.store'), $invalidData)
        ->assertStatus(422)
        ->assertJsonValidationErrors($expectedErrors);
})->with([
    'missing name' => [
        'invalidData' => [
            'location' => ['type' => 'Point', 'coordinates' => [-0.1, 51.5]],
        ],
        'expectedErrors' => 'name',
    ],
    'missing location' => [
        'invalidData' => [
            'name' => 'Test Clinic',
        ],
        'expectedErrors' => 'location',
    ],
    'invalid location (not GeoJSON)' => [
        'invalidData' => [
            'name' => 'Test Clinic',
            'location' => 'not-a-geojson',
        ],
        'expectedErrors' => 'location',
    ],
    'invalid location (wrong geometry type)' => [
        'invalidData' => [
            'name' => 'Test Clinic',
            'location' => ['type' => 'Polygon', 'coordinates' => []],
        ],
        'expectedErrors' => 'location',
    ],
]);

it('returns autocomplete results', function () {
    $clinics = Clinic::factory()->count(3)->create(['name' => 'Test Clinic']);

    $this->mock(ClinicServiceInterface::class)
        ->shouldReceive('autocomplete')
        ->once()
        ->with('Test')
        ->andReturn($clinics);

    $this->get(route('clinics.autocomplete', ['query' => 'Test']))
        ->assertOk()
        ->assertJsonCount(3)
        ->assertJsonFragment(['name' => 'Test Clinic']);
});
