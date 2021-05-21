<?php

declare(strict_types=1);

namespace HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch;

use Illuminate\Support\Collection;
use LaravelDoctrine\Scout\SearchableRepository;

class DoctrineHitsIteratorAggregate implements HitsIteratorAggregate
{
    private array $results;

    /**
     * @var callable|null
     */
    private $callback;

    private ?SearchableRepository $repository;

    public function __construct(array $results, callable $callback = null, ?SearchableRepository $repository = null)
    {
        $this->results    = $results;
        $this->callback   = $callback;
        $this->repository = $repository;
    }

    public function getIterator(): \ArrayIterator
    {
        if ($this->results['hits']['total'] <= 0) {
            return new \ArrayIterator([]);
        }

        $hits = $this->results['hits']['hits'];

        $entities = collect($hits)->groupBy('_source.__class_name')
            ->map(
                function (Collection $results, $class) {
                    return $this->repository->whereIn('id', $results->pluck('_id')->all())->get();
                }
            )
            ->flatten()->keyBy(
                function ($entity) {
                    return get_class($entity) . '::' . $entity->getScoutKey();
                }
            );

        $hits = collect($hits)->map(function ($hit) use ($entities) {
            $key = $hit['_source']['__class_name'] . '::' . $hit['_id'];

            return $entities[$key] ?? null;
        })->filter()->all();

        return new \ArrayIterator((array)$hits);
    }
}
