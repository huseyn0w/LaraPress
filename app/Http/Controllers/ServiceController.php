<?php

namespace App\Http\Controllers;

use App\Services\Front\ServiceViewService;

class ServiceController extends BaseController
{
    public function __construct(ServiceViewService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Public services grid (published, ordered by sort_order). Honours an
     * optional locale prefix by switching the session locale (mirrors the
     * BaseController slug-locale handling).
     */
    public function listing(?string $locale = null)
    {
        if (! is_null($locale) && in_array($locale, get_lang_prefixes()) && $locale !== get_current_lang()) {
            return $this->setLang($locale);
        }

        return view('default.services.index', [
            'services' => $this->service->publishedOrdered(),
        ]);
    }

    /**
     * Single published service by slug. Draft/private services resolve to null
     * (front read scope) and 404, identical to the post/page detail paths.
     */
    public function show(string $slug, ?string $locale = null)
    {
        $result = parent::index($slug, $locale);

        if (is_object($result)) {
            return $result;
        }

        return view('default.services.show', ['data' => $this->data]);
    }
}
