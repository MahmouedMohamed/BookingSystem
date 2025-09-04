<?php

namespace App\Modules\Bookings\Models;

use App\Exceptions\ModelNotFoundException;
use App\Modules\Bookings\Observers\BookingObserver;
use App\Modules\Services\Models\Service;
use App\Modules\Users\Models\User;
use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([BookingObserver::class])]
class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bookings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'provider_id',
        'service_id',
        'start_date',
        'end_date',
        'status',
        'cancelled_by',
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
        return [];
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function provider()
    {
        return $this->belongsTo(Service::class, 'provider_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function scopeProvider(Builder $query, $providerId)
    {
        return $query->where('provider_id', '=', $providerId);
    }

    public function scopeCustomer(Builder $query, $customerId)
    {
        return $query->where('customer_id', '=', $customerId);
    }

    protected static function newFactory()
    {
        return BookingFactory::new();
    }
}
