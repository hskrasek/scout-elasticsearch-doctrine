<?php

declare(strict_types=1);

namespace HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch\Params\Indices\Alias;

final class Get
{
    private string $alias;

    private string $index;

    public function __construct(string $alias, string $index = '*')
    {
        $this->alias = $alias;
        $this->index = $index;
    }

    public static function anyIndex(string $alias): Get
    {
        return new self($alias, '*');
    }

    public function toArray(): array
    {
        return [
            'index' => $this->index,
            'name'  => $this->alias,
        ];
    }
}
