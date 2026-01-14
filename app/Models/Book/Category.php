<?php

namespace App\Models\Book;

use Database\Factories\Book\CategoryFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Category extends Model implements Sortable
{
    use SortableTrait,HasFactory;

    protected $fillable = ['parent_id', 'name', 'slug', 'order_column'];


    public $sortable = [
        'order_column_name' => 'order_column',
        'sort_when_creating' => true,
    ];

    /**
     * Use slug for route model binding (Senior Practice)
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted()
    {
        static::saving(function ($category) {
            if (empty($category->slug)) {
                $category->slug = $category->name;
            }
        });
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::lower(Str::slug($value));
    }

    public function buildSortQuery(): Builder
    {
        return static::query()->where('parent_id', $this->parent_id);
    }


    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->ordered();
    }

    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

}
