<?php declare(strict_types=1);

namespace HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch;

class DoctrineHitsIteratorAggregate implements HitsIteratorAggregate
{
    private array $results;

    /**
     * @var callable
     */
    private $callback;

    public function __construct(array $results, callable $callback)
    {
        $this->results  = $results;
        $this->callback = $callback;
    }

    public function getIterator()
    {
        // TODO: Implement getIterator() method.
    }
}
