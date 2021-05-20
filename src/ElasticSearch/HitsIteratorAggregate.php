<?php

declare(strict_types=1);

namespace HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch;

interface HitsIteratorAggregate extends \IteratorAggregate
{
    public function __construct(array $results, callable $callback = null);
}
