<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ClinicServiceInterface
{
    public function getClinics(): LengthAwarePaginator;

    public function getNearbyClinics(float $lat, float $lng, int $radius): Collection;

    public function autocomplete(string $query): Collection;
}
