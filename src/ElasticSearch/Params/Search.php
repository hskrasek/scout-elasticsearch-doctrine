<?php

declare(strict_types=1);

namespace HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch\Params;

/**
 * @internal
 */
final class Search
{
    private string $index;
    private array $body;

    public function __construct(string $index, array $body)
    {
        $this->index = $index;
        $this->body  = $body;
    }

    public function toArray(): array
    {
        return [
            'index' => $this->index,
            'body'  => $this->body,
        ];
    }
}
