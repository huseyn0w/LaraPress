<?php

namespace App\Repositories;

use App\Http\Models\Service;
use App\Http\Models\ServiceTranslation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ServiceRepository extends BaseRepository
{
    protected $main_table = 'services';

    protected $translated_table = 'service_translations';

    protected $translated_table_join_column = 'service_id';

    /**
     * Services have no author relation — disable eager-loading inherited from
     * BaseRepository so getTranslatedBy() does not throw RelationNotFoundException.
     *
     * @var array<int, string>
     */
    protected $eager_relations = [];

    protected $select_fields = [
        'id',
        'sort_order',
        'title',
        'slug',
        'icon',
        'excerpt',
        'content',
        'thumbnail',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'meta_noindex',
        'status',
    ];

    public function __construct(Service $model)
    {
        parent::__construct();
        $this->model = $model;

        $this->translated_table_model = new ServiceTranslation;
    }

    /**
     * Restrict public single-service reads to PUBLISHED services only.
     * Status lives on service_translations (1 = published, 0 = draft/private).
     */
    protected function applyFrontReadScope($query)
    {
        return $query->where('service_translations.status', '=', Service::STATUS_PUBLISHED);
    }

    /**
     * Published services for the current (or given) locale, ordered for the
     * public grid by sort_order then id. sort_order lives on the base table.
     */
    public function publishedOrdered(?string $locale = null): Collection
    {
        $locale = $locale ?: app()->getLocale();

        return DB::table('services')
            ->join('service_translations', 'services.id', '=', 'service_translations.service_id')
            ->where('service_translations.locale', $locale)
            ->where('service_translations.status', Service::STATUS_PUBLISHED)
            ->whereNull('services.deleted_at')
            ->orderBy('services.sort_order')
            ->orderBy('services.id')
            ->select(
                'services.id',
                'services.sort_order',
                'service_translations.title',
                'service_translations.slug',
                'service_translations.icon',
                'service_translations.excerpt',
                'service_translations.content',
                'service_translations.thumbnail',
                'service_translations.meta_description',
                'service_translations.meta_keywords',
                'service_translations.canonical_url',
                'service_translations.meta_noindex',
                'service_translations.status',
            )
            ->get();
    }

    /**
     * Sitemap rows for published services: one row per (service, locale)
     * translation with slug + updated_at for <url>/<lastmod> entries.
     */
    public function sitemapEntries(): Collection
    {
        return DB::table('service_translations')
            ->join('services', 'services.id', '=', 'service_translations.service_id')
            ->whereNull('services.deleted_at')
            ->where('service_translations.status', Service::STATUS_PUBLISHED)
            ->select('service_translations.slug', 'service_translations.locale', 'service_translations.updated_at')
            ->get();
    }
}
