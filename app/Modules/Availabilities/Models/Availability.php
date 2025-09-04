<?php

namespace App\Modules\Availabilities\Models;

use App\Exceptions\ModelNotFoundException;
use App\Modules\Availabilities\Observers\AvailabilityObserver;
use Database\Factories\AvailabilityFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([AvailabilityObserver::class])]
class Availability extends Model
{
    use HasFactory;

    protected $table = 'provider_availabilities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'provider_id',
        'weekday',
        'start',
        'end',
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        $model = static::where('id', $value)->first();
        if (empty($model)) {
            throw new ModelNotFoundException;
        }

        return $model;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
        ];
    }

    protected static function newFactory()
    {
        return AvailabilityFactory::new();
    }
}
