<?php

namespace App\Http\Controllers\CPanel;

use App\Http\Requests\ValidateSeoSettings;
use App\Repositories\CPanelSeoSettingsRepository;

/**
 * Phase 7 (SEO/GEO): admin SEO settings page (global, singleton row id = 1).
 * Gated by the manage_general_settings middleware on the route group.
 */
class CPanelSeoSettingsController extends CPanelBaseController
{
    public function __construct(CPanelSeoSettingsRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function index()
    {
        $seo_settings = $this->repository->firstOrNew();

        return view('cpanel.settings.seo-settings', compact('seo_settings'));
    }

    public function store(ValidateSeoSettings $request)
    {
        $instance = $this->repository->firstOrNew();
        $instance->fill($request->validated());
        $result = $instance->save();

        return back()->with('message', $result);
    }
}
