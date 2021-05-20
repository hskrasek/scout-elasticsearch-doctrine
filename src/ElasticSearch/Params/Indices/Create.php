<?php

declare(strict_types=1);

namespace HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch\Params\Indices;

final class Create
{
    private string $index;

    private array $config;

    public function __construct(string $index, array $config)
    {
        $this->index  = $index;
        $this->config = $config;
    }

    public function toArray(): array
    {
        return [
            'index' => $this->index,
            'body'  => $this->config,
        ];
    }
}
