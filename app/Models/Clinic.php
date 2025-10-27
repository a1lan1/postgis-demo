<?php

namespace App\Models;

use Clickbar\Magellan\Data\Geometries\Point;
use Clickbar\Magellan\Database\PostgisFunctions\ST;
use Database\Factories\ClinicFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $name
 * @property Point $location
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 *
 * @method static \Database\Factories\ClinicFactory factory($count = null, $state = [])
 * @method static Builder<static>|Clinic nearTo(\Clickbar\Magellan\Data\Geometries\Point $point, float $radius)
 * @method static Builder<static>|Clinic newModelQuery()
 * @method static Builder<static>|Clinic newQuery()
 * @method static Builder<static>|Clinic query()
 * @method static Builder<static>|Clinic whereCreatedAt($value)
 * @method static Builder<static>|Clinic whereId($value)
 * @method static Builder<static>|Clinic whereLocation($value)
 * @method static Builder<static>|Clinic whereName($value)
 * @method static Builder<static>|Clinic whereUpdatedAt($value)
 * @method static Builder<static>|Clinic whereUserId($value)
 * @method static Builder<static>|Clinic withinBoundingBox(float $minLng, float $minLat, float $maxLng, float $maxLat, int $srid = 4326)
 *
 * @mixin \Eloquent
 */
class Clinic extends Model
{
    /** @use HasFactory<ClinicFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Clinics within a radius (meters)
     */
    public function scopeNearTo(Builder $query, Point $point, float $radius): Builder
    {
        $location = DB::raw('location::geometry');

        return $query->where(ST::distanceSphere($point, $location), '<=', $radius);
    }

    /**
     * Clinics inside the bounding box (visible area of the map).
     */
    public function scopeWithinBoundingBox(
        Builder $query,
        float $minLng,
        float $minLat,
        float $maxLng,
        float $maxLat,
        int $srid = 4326
    ): Builder {
        return $query->whereRaw(
            'ST_Within(location, ST_MakeEnvelope(?, ?, ?, ?, ?))',
            [$minLng, $minLat, $maxLng, $maxLat, $srid]
        );
    }

    protected function casts(): array
    {
        return [
            'location' => Point::class,
        ];
    }
}
