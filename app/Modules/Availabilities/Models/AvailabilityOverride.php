<?php

namespace App\Modules\Availabilities\Models;

use App\Exceptions\ModelNotFoundException;
use App\Modules\Availabilities\Observers\AvailabilityOverrideObserver;
use Database\Factories\AvailabilityOverrideFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([AvailabilityOverrideObserver::class])]
class AvailabilityOverride extends Model
{
    use HasFactory;

    protected $table = 'provider_availabilities_overrides';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'provider_id',
        'date',
        'weekday',
        'recurring',
        'number_of_recurring',
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
            'date' => 'date',
        ];
    }

    protected static function newFactory()
    {
        return AvailabilityOverrideFactory::new();
    }
}
