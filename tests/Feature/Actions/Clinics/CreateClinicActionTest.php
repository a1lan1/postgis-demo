<?php

use App\Actions\Clinics\CreateClinicAction;
use App\DTOs\Clinics\CreateClinicDTO;
use App\Events\NewClinicAdded;
use App\Models\User;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Support\Facades\Event;

it('should create a clinic and associate it with a user', function () {
    Event::fake();

    $user = User::factory()->create();
    $action = app(CreateClinicAction::class);
    $location = Point::makeGeodetic(latitude: 43.238949, longitude: 76.889709);

    $dto = new CreateClinicDTO(
        name: 'My Awesome Clinic',
        location: $location,
        user: $user
    );

    $clinic = $action($dto);

    $this->assertDatabaseHas('clinics', [
        'id' => $clinic->id,
        'name' => 'My Awesome Clinic',
        'user_id' => $user->id,
    ]);

    $this->assertEquals($location->getLatitude(), $clinic->location->getLatitude());
    $this->assertEquals($location->getLongitude(), $clinic->location->getLongitude());

    Event::assertDispatched(NewClinicAdded::class, function ($event) use ($clinic) {
        return $event->clinic->is($clinic);
    });
});

it('should create a clinic without a user for a guest', function () {
    Event::fake();

    $action = app(CreateClinicAction::class);
    $location = Point::makeGeodetic(latitude: 43.238949, longitude: 76.889709);

    $dto = new CreateClinicDTO(
        name: 'Guest Clinic',
        location: $location,
        user: null
    );

    $clinic = $action($dto);

    $this->assertDatabaseHas('clinics', [
        'id' => $clinic->id,
        'name' => 'Guest Clinic',
        'user_id' => null,
    ]);

    $this->assertEquals($location->getLatitude(), $clinic->location->getLatitude());
    $this->assertEquals($location->getLongitude(), $clinic->location->getLongitude());

    Event::assertDispatched(NewClinicAdded::class, function ($event) use ($clinic) {
        return $event->clinic->is($clinic);
    });
});
