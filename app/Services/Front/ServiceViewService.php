<?php

namespace App\Services\Front;

use App\Repositories\ServiceRepository;
use App\Services\BaseCrudService;
use Illuminate\Support\Collection;

class ServiceViewService extends BaseCrudService
{
    public function __construct(private ServiceRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Published services for the public grid, ordered by sort_order.
     */
    public function publishedOrdered(?string $locale = null): Collection
    {
        return $this->repo->publishedOrdered($locale);
    }
}
