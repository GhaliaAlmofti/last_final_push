<?php

namespace App\Models\Book;

use App\Models\AuthorBook;
use App\Models\Media\Media;
use App\Models\User;
use App\Models\UsersType\Author;
use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory, SoftDeletes, HasMedia;
    protected $fillable = [
            'category_id',
            'title',
            'slug',
            'isbn',
            'price',
            'publish_year',
            'stock',
            'is_active',
        ];
    protected $casts = [
        'publish_year' => 'integer',
        'stock' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($book) {
            if (empty($book->slug)) {
                $book->slug = Str::slug($book->title) . '-' . Str::lower(Str::random(5));
            }
        });
    }
    public function owner()
    {
        return $this->authors()->wherePivot('is_owner', true);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'author_book')
            ->withPivot('is_owner')
            ->withTimestamps();
    }

        public function scopeWhereCategory($query, $categoryId)
    {
        return $query->whereHas('subCategory', function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

}
