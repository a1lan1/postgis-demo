<?php

namespace App\DTOs\Clinics;

use App\Models\User;
use Clickbar\Magellan\Data\Geometries\Point;

readonly class CreateClinicDTO
{
    public function __construct(
        public string $name,
        public Point $location,
        public ?User $user = null
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'location' => $this->location,
            'user_id' => $this->user?->id,
        ];
    }
}
