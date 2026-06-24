<?php

namespace App\Http\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

/**
 * Post tag (FEATURE_MATRIX §2): a lean translatable taxonomy — per-locale
 * name + slug, many-to-many with posts. No hierarchy (that is Category's role).
 *
 * @property-read string $name
 * @property-read string $slug
 */
class Tag extends Model implements TranslatableContract
{
    use Cachable;
    use Translatable;

    public $translatedAttributes = [
        'name',
        'slug',
    ];

    public $timestamps = false;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag');
    }
}
