<?php

namespace App\Modules\Services\Models;

use App\Exceptions\ModelNotFoundException;
use App\Modules\Services\Observers\ServiceObserver;
use App\Modules\Users\Models\User;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([ServiceObserver::class])]
class Service extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'provider_id',
        'name',
        'description',
        'category_id',
        'duration',
        'price',
        'is_published'
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

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id', 'id');
    }

    /**
     * Scope to search services.
     */
    public function scopeSearch($query, string $searchKey)
    {
        return $query->where(function ($q) use ($searchKey) {
            $q->where('name', 'like', "%{$searchKey}%")
                ->orWhere('description', 'like', "%{$searchKey}%");
        });
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', '=', true);
    }

    public function scopeProvider($query, $providerId)
    {
        return $query->where('provider_id', '=', $providerId);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    protected static function newFactory()
    {
        return ServiceFactory::new();
    }
}
