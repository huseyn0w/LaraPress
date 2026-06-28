<?php

namespace App\Http\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model implements TranslatableContract
{
    use Cachable;
    use SoftDeletes;
    use Translatable;

    public const STATUS_PUBLISHED = 1;

    public $timestamps = false;

    protected $fillable = ['sort_order'];

    public $translatedAttributes = [
        'service_id', 'locale', 'title', 'slug', 'icon', 'excerpt', 'content',
        'thumbnail', 'meta_description', 'meta_keywords', 'canonical_url',
        'meta_noindex', 'status', 'created_at', 'updated_at',
    ];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
