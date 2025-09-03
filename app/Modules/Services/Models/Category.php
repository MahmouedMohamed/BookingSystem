<?php

namespace App\Modules\Services\Models;

use App\Exceptions\ModelNotFoundException;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
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

    /**
     * Scope to search users.
     */
    public function scopeSearch($query, string $searchKey)
    {
        return $query->where(function ($q) use ($searchKey) {
            $q->where('name', 'like', "%{$searchKey}%");
        });
    }

    protected static function newFactory()
    {
        return CategoryFactory::new();
    }
}
