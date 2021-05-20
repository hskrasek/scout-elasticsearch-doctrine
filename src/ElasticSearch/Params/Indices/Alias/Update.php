<?php

declare(strict_types=1);

namespace HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch\Params\Indices\Alias;

final class Update
{
    private array $actions;

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    public function add(string $index, string $alias): void
    {
        $this->actions[] = [
            'add' => [
                'index' => $index,
                'alias' => $alias,
            ],
        ];
    }

    public function removeIndex(string $index): void
    {
        $this->actions[] = ['remove_index' => ['index' => $index]];
    }

    public function toArray(): array
    {
        return [
            'body' => [
                'actions' => $this->actions,
            ],
        ];
    }
}
