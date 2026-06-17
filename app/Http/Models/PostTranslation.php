<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class PostTranslation extends Model
{
    use Cachable;

    protected $fillable = [
        'title',
        'post_id',
        'locale',
        'updated_at',
        'created_at',
        'likes',
        'author_id',
        'slug',
        'thumbnail',
        'preview',
        'content',
        'meta_description',
        'status',
        'meta_keywords',
        'canonical_url',
        'meta_noindex'
    ];
}
