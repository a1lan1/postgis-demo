<?php

namespace App\Services;

use App\Contracts\ClinicServiceInterface;
use App\Models\Clinic;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ClinicService implements ClinicServiceInterface
{
    private const int CLINICS_PAGINATE = 50;

    public function getClinics(): LengthAwarePaginator
    {
        return Clinic::query()
            ->with('user:id,name')
            ->paginate(self::CLINICS_PAGINATE);
    }

    public function getNearbyClinics(float $lat, float $lng, int $radius): Collection
    {
        $point = Point::makeGeodetic($lat, $lng);

        return Clinic::query()
            ->with('user:id,name')
            ->nearTo($point, $radius)
            ->get();
    }

    public function autocomplete(string $query): Collection
    {
        return Clinic::query()
            ->where('name', 'like', "%{$query}%")
            ->select(['id', 'name', 'location'])
            ->limit(10)
            ->get();
    }
}
