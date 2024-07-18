<?php

namespace EinarHansen\FootballData\Pagination;

use ArrayIterator;
use Closure;
use EinarHansen\Http\Contracts\Data\Data;
use EinarHansen\Http\Contracts\Pagination\Paginator as PaginatorContract;
use EinarHansen\Http\Contracts\Resource\Resource;
use Generator;
use Iterator;
use IteratorAggregate;
use Traversable;

class Paginator implements PaginatorContract
{
    /**
     * Pass closure or array if you want to run the collection multiple times, or
     * pass a Generator if you only want to run it one
     *
     * @param  Traversable<int, Data>|array<int, Data>|(Closure(): Generator<int, Data>)  $items
     */
    public function __construct(
        public readonly Closure|iterable $items,
        public readonly int $perPage,
        public readonly int $currentPage = 1,
        public readonly ?Closure $nextPage = null,
        public readonly ?Closure $previousPage = null,
        public readonly ?Resource $resource = null,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function currentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * {@inheritDoc}
     */
    public function nextPage(): ?static
    {
        if (! $this->hasMorePages()) {
            return null;
        }

        if (is_null($this->nextPage)) {
            return null;
        }

        return ($this->nextPage)();
    }

    /**
     * {@inheritDoc}
     */
    public function previousPage(): ?static
    {
        if ($this->currentPage() <= 1) {
            return null;
        }

        if (is_null($this->previousPage)) {
            return null;
        }

        return ($this->previousPage)();
    }

    /**
     * {@inheritDoc}
     */
    public function items(): iterable
    {
        return $this->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function firstItem(): int
    {
        return $this->perPage() * $this->currentPage();
    }

    /**
     * {@inheritDoc}
     */
    public function lastItem(): int
    {
        return $this->count() > 0 ? $this->firstItem() + $this->count() - 1 : $this->firstItem();
    }

    /**
     * Get the first item in the collection
     */
    public function first(): ?Data
    {
        foreach ($this as $value) {
            return $value;
        }

        return null;
    }

    /**
     * Get the last item in the collection
     */
    public function last(): ?Data
    {
        $needle = null;

        foreach ($this as $value) {
            $needle = $value;
        }

        return $needle;
    }

    /**
     * Get an item by key.
     */
    public function get(int $key): ?Data
    {
        foreach ($this as $outerKey => $outerValue) {
            if ($outerKey == $key) {
                return $outerValue;
            }
        }

        return null;
    }

    /**
     * Reset the keys on the underlying array.
     */
    public function values(): static
    {
        return new Paginator(
            items: function () {
                foreach ($this as $item) {
                    yield $item;
                }
            },
            perPage: $this->perPage,
            currentPage: $this->currentPage,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /**
     * {@inheritDoc}
     */
    public function hasMorePages(): bool
    {
        return $this->count() === $this->perPage();
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        if (is_array($this->items)) {
            return count($this->items);
        }

        return iterator_count($this->getIterator());
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty(): bool
    {
        $iterator = $this->getIterator();
        if ($iterator instanceof Iterator) {
            return $iterator->valid();
        }

        return $this->count() > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    /**
     * @return Traversable<int, Data>
     */
    public function getIterator(): Traversable
    {
        return $this->makeIterator($this->items);
    }

    /**
     * Make an iterator from the given source.
     *
     * @param  Traversable<int, Data>|array<int, Data>|(Closure(): Generator<int, Data>)  $source
     * @return Traversable<int, Data>
     */
    protected function makeIterator($source)
    {
        if ($source instanceof IteratorAggregate) {
            /** @var Traversable<int, Data> $items */
            $items = $source->getIterator();

            return $items;
        }

        if (is_array($source)) {
            return new ArrayIterator($source);
        }

        if ($source instanceof Traversable) {
            return $source;
        }

        return $source();
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        if (is_array($this->items)) {
            return $this->items;
        }

        return iterator_to_array(
            iterator: $this->getIterator(),
            preserve_keys: false
        );
    }
}
