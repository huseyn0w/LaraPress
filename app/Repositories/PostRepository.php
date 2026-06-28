<?php

/**
 * Cmstack-Laravel
 * File: PageRepository.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 24.10.2019
 */

namespace App\Repositories;

use App\Http\Models\Likes;
use App\Http\Models\Post;
use App\Http\Models\PostTranslation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class PostRepository extends BaseRepository
{
    protected $main_table = 'posts';

    protected $translated_table = 'post_translations';

    protected $translated_table_join_column = 'post_id';

    protected $select_fields = [
        'id',
        'author_id',
        'title',
        'content',
        'likes',
        'thumbnail',
        'slug',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'meta_noindex',
        'status',
        'created_at',
        'updated_at',
    ];

    public function __construct(Post $model)
    {
        parent::__construct();
        $this->model = $model;
        $this->translated_table_model = new PostTranslation;
    }

    /**
     * Sitemap rows for all posts: one row per (post, locale) translation with
     * the slug + updated_at used to build <url>/<lastmod>/<xhtml:link> entries.
     */
    public function sitemapEntries()
    {
        return Post::join('post_translations', 'posts.id', '=', 'post_translations.post_id')
            ->select('posts.id', 'post_translations.slug', 'post_translations.locale', 'post_translations.updated_at', 'post_translations.post_id')
            ->notScheduledForFuture()
            ->get();
    }

    /**
     * Feed rows for the public syndication feeds (RSS/Atom): the most recent
     * PUBLISHED posts in the given locale, newest first. Drafts (status != 1) and
     * future-scheduled posts are excluded so unpublished content never leaks.
     *
     * @return Collection
     */
    public function feedEntries(string $locale, int $limit = 20)
    {
        return Post::join('post_translations', 'posts.id', '=', 'post_translations.post_id')
            ->select(
                'posts.id',
                'post_translations.title',
                'post_translations.slug',
                'post_translations.preview',
                'post_translations.content',
                'post_translations.created_at',
                'post_translations.updated_at',
            )
            ->where('post_translations.locale', $locale)
            ->where('post_translations.status', Post::STATUS_PUBLISHED)
            ->notScheduledForFuture()
            ->orderByDesc('post_translations.created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Restrict public single-post reads to PUBLISHED posts only, while also
     * hiding future-scheduled drafts (notScheduledForFuture). The two
     * constraints together mean:
     *   - status must be PUBLISHED (1) — plain drafts are always hidden.
     *   - a published post with a lingering future schedule IS still visible
     *     (the notScheduledForFuture scope's OR-status=1 branch allows it).
     *
     * Column is post_translations.status (status lives on the translations table).
     *
     * @param  mixed  $query
     * @return mixed
     */
    protected function applyFrontReadScope($query)
    {
        return $query
            ->where('post_translations.status', '=', Post::STATUS_PUBLISHED)
            ->notScheduledForFuture();
    }

    public function handleLike(int $post_id, int $user_id)
    {
        if (Auth::user()->id !== $user_id) {
            return false;
        }

        $result = false;

        $data = Likes::where('post_id', $post_id)->where('user_id', $user_id)->first();
        if (empty($data)) {
            $like_added = Likes::insert([
                ['user_id' => $user_id, 'post_id' => $post_id],
            ]);

            if ($like_added) {
                PostTranslation::where('post_id', $post_id)->increment('likes');
                $result = trans('default/post.like_added');
            }
        } else {
            $like_deleted = Likes::where('post_id', $post_id)->where('user_id', $user_id)->delete();
            if ($like_deleted) {
                PostTranslation::where('post_id', $post_id)->decrement('likes');
                $result = trans('default/post.like_deleted');
            }
        }

        return $result;
    }

    public function getTranslatedBy($param, $value)
    {
        $comments_per_page = get_comments_count_per_page();
        $data = parent::getTranslatedBy($param, $value);

        // A non-existent slug yields null here; let the caller surface the 404
        // (BaseController::index throwNotFound) instead of fatally calling
        // setRelation() on null.
        if (is_null($data)) {
            return null;
        }

        $data->setRelation('comments', $data->comments()->with('replies')->with('user')->orderBy('id', 'DESC')->paginate($comments_per_page));

        return $data;
    }
}
