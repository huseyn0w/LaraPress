<?php

namespace App\Http\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model implements TranslatableContract
{
    use Cachable;
    use SoftDeletes;
    use Translatable;

    /** Published status value on post_translations.status (0 = private/draft). */
    public const STATUS_PUBLISHED = 1;

    public $timestamps = false;

    public $translatedAttributes = [
        'title',
        'post_id',
        'created_at',
        'updated_at',
        'author_id',
        'slug',
        'thumbnail',
        'preview',
        'status',
        'content',
        'meta_keywords',
        'meta_description',
        'scheduled_at',
    ];

    /**
     * The `posts` table only stores the primary key and soft-delete timestamp;
     * all editable content lives in the `post_translations` table (see
     * PostTranslation). Nothing on the main row is mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    public function author()
    {
        return $this->hasOne('App\Http\Models\User', 'id', 'author_id');
    }

    /**
     * Restrict a query (joined to post_translations) to posts that are NOT a
     * pending future-scheduled draft. A post is hidden only while it is awaiting
     * its schedule — i.e. it is NOT yet published (status != 1) AND its
     * scheduled_at is still in the future. Already-published posts are always
     * visible (publishing overrides a lingering schedule), and posts with no
     * schedule are unaffected. Used by every public read path.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeNotScheduledForFuture($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('post_translations.scheduled_at')
                ->orWhere('post_translations.scheduled_at', '<=', now())
                ->orWhere('post_translations.status', '=', self::STATUS_PUBLISHED);
        });
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_post');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public function likes()
    {
        return $this->belongsTo(Likes::class);
    }

    public function comments()
    {
        return $this->hasMany(Comments::class)
            ->whereNull('parent_id')
            ->where('status', 1)
            ->with('user')
            ->with('replies');
    }

    public function allCommentsCount()
    {
        return $this->hasMany(Comments::class)->where('status', 1);
    }
}
