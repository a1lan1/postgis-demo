<?php

namespace App\Actions\Clinics;

use App\DTOs\Clinics\CreateClinicDTO;
use App\Events\NewClinicAdded;
use App\Models\Clinic;

class CreateClinicAction
{
    public function __invoke(CreateClinicDTO $dto): Clinic
    {
        $clinic = Clinic::create($dto->toArray());

        event(new NewClinicAdded($clinic));

        return $clinic;
    }
}
