<?php

namespace App\Services\CPanel;

use App\Repositories\CPanelServiceRepository;
use App\Services\BaseCrudService;

class CPanelServiceService extends BaseCrudService
{
    public function __construct(private CPanelServiceRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Paginated list of soft-deleted services for the trash tab.
     */
    public function trashed($count)
    {
        return $this->repo->trashed($count);
    }

    /**
     * Apply a bulk row action selected on the list screen.
     */
    public function runBulkAction(string $action, array $ids): void
    {
        match ($action) {
            'restore' => $this->repo->restore($ids),
            'destroy' => $this->repo->destroy($ids),
            'delete' => $this->repo->delete($ids),
            default => null,
        };
    }
}
