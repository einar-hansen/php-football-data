<?php

namespace EinarHansen\FootballData\Pagination;

use EinarHansen\FootballData\Data\Team;
use EinarHansen\FootballData\Resources\TeamResource;
use IteratorAggregate;

/**
 * @property TeamResource $resource
 *
 * @implements IteratorAggregate<int, Team>
 */
class TeamPaginator extends Paginator implements IteratorAggregate
{
    /**
     * {@inheritDoc}
     */
    public function nextPage(): ?static
    {
        if ($this->hasMorePages()) {
            return $this->resource
                ->paginate(
                    limit: $this->perPage(),
                    page: $this->currentPage() + 1
                );
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function previousPage(): ?static
    {
        if ($this->currentPage() > 1) {
            return $this->resource
                ->paginate(
                    limit: $this->perPage(),
                    page: $this->currentPage() - 1
                );
        }

        return null;
    }
}
