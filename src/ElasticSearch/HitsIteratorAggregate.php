<?php

declare(strict_types=1);

namespace HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch;

use LaravelDoctrine\Scout\SearchableRepository;

interface HitsIteratorAggregate extends \IteratorAggregate
{
    public function __construct(array $results, ?callable $callback = null, ?SearchableRepository $repository);
}
