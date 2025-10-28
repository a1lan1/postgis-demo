<?php

namespace Tests\Unit\Services;

use App\Models\Clinic;
use App\Services\ClinicService;
use Illuminate\Pagination\LengthAwarePaginator;

beforeEach(function () {
    $this->clinicService = new ClinicService;
});

it('paginates all clinics', function () {
    Clinic::factory()->count(20)->create();

    $result = $this->clinicService->getClinics();

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class)
        ->and($result->count())->toBe(20);
});

it('finds nearby clinics', function () {
    Clinic::factory()->create([
        'name' => 'Some Clinic',
        'location' => 'POINT(-0.1278 51.5074)',
    ]);

    Clinic::factory()->create([
        'name' => 'Another Clinic',
        'location' => 'POINT(2.3522 48.8566)',
    ]);

    $result = $this->clinicService->getNearbyClinics(51.5075, -0.1279, 10000);

    expect($result->count())->toBe(1)
        ->and($result->first()->name)->toBe('Some Clinic');
});

it('returns autocomplete suggestions', function () {
    Clinic::factory()->create(['name' => 'Main Street Clinic']);
    Clinic::factory()->create(['name' => 'Central Clinic']);
    Clinic::factory()->create(['name' => 'Main Avenue Practice']);

    $result = $this->clinicService->autocomplete('Main');

    expect($result->count())->toBe(2)
        ->and($result->pluck('name')->all())->toBe(['Main Street Clinic', 'Main Avenue Practice']);
});
