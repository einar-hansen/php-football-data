<?php

declare(strict_types=1);

namespace EinarHansen\FootballData\Data;

use EinarHansen\Http\Contracts\Data\Data;

class Area implements Data
{
    /**
     * @param  \EinarHansen\FootballData\Data\Area[]  $children
     */
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $code,
        public readonly ?string $image = null,
        public readonly ?int $parentId = null,
        public readonly ?string $parentName = null,
        public readonly array $children = [],
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'image' => $this->image,
            'parentId' => $this->parentId,
            'parentName' => $this->parentName,
            'children' => array_map(
                array: $this->children,
                callback: fn (Area $child) => $child->toArray()
            ),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
