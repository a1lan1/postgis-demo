<?php

namespace App\Models;

use Clickbar\Magellan\Data\Geometries\Point;
use Database\Factories\ClinicFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Point $location
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\ClinicFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Clinic query()
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

    protected function casts(): array
    {
        return [
            'location' => Point::class,
        ];
    }
}
