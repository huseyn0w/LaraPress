<?php

namespace App\Services\CPanel;

use App\Repositories\CPanelPageRepository;
use App\Repositories\RevisionRepository;
use App\Services\BaseCrudService;
use App\Services\Concerns\ManagesRevisions;

/**
 * Domain service for CPanel page administration. Owns all data access for the
 * page controller via CPanelPageRepository; inherits generic CRUD from
 * BaseCrudService and returns domain results (never HTTP responses).
 */
class CPanelPageService extends BaseCrudService
{
    use ManagesRevisions;

    public function __construct(private CPanelPageRepository $repo, RevisionRepository $revisions)
    {
        parent::__construct($repo);
        $this->revisions = $revisions;
    }
}
