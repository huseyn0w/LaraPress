<?php
/**
 * Cmstack-Laravel
 * File: CPanelSeoSettingsRepository.php
 * Phase 7 (SEO/GEO): persistence for the global SEO settings singleton.
 */

namespace App\Repositories;

use App\Http\Models\CPanel\CPanelSeoSettings;

class CPanelSeoSettingsRepository extends BaseRepository
{
    public function __construct(CPanelSeoSettings $model)
    {
        parent::__construct();
        $this->model = $model;
    }

    /**
     * Always return a model instance even on a fresh DB so the settings form
     * can bind to it (singleton row id = 1).
     */
    public function firstOrNew()
    {
        return $this->model::firstOrNew(['id' => 1]);
    }
}
