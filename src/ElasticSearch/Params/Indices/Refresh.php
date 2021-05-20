<?php

declare(strict_types=1);

namespace HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch\Params\Indices;

final class Refresh
{
    private string $index;

    public function __construct(string $index)
    {
        $this->index = $index;
    }

    public function toArray(): array
    {
        return [
            'index' => $this->index,
        ];
    }
}
