<?php

declare(strict_types=1);

namespace HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch\Params;

final class Bulk
{
    private array $indexDocs = [];

    private array $deleteDocs = [];

    /**
     * @param iterable|object $docs
     */
    public function delete($docs): void
    {
        //TODO: Add assertions or is_iterable check to throw an exception
        //TODO: Add instance of LaravelDoctrine Searchable
        if (is_object($docs)) {
            $this->deleteDocs[$docs->getKey()] = $docs;

            return;
        }

        foreach ($docs as $doc) {
            $this->delete($doc);
        }
    }

    public function index($docs): void
    {
        if (is_object($docs)) {
            $this->indexDocs[$docs->getKey()] = $docs;

            return;
        }

        foreach ($docs as $doc) {
            $this->index($doc);
        }
    }

    public function toArray(): array
    {
        $payload = ['body' => []];

        $payload = collect($this->indexDocs)->reduce(
            function (array $payload, $entity) {
                // TODO: Handle soft delete
                $scoutKey = $entity->getKey();

                $payload['body'][] = [
                    'index' => [
                        '_index'  => $entity->searchableAs(),
                        '_id'     => $scoutKey,
                        '_type'   => 'doc',
                        'routing' => $scoutKey,
                    ],
                ];

                $payload['body'][] = array_merge(
                    $entity->toSearchableArray(),
                    $entity->scoutMetadata(),
                    [
                        '__class_name' => get_class($entity),
                    ]
                );

                return $payload;
            },
            $payload
        );

        $payload = collect($this->deleteDocs)->reduce(
            function (array $payload, $entity) {
                $scoutKey = $entity->getKey();

                $payload['body'][] = [
                    'delete' => [
                        '_index'  => $entity->searchableAs(),
                        '_id'     => $scoutKey,
                        '_type'   => 'doc',
                        'routing' => $scoutKey,
                    ],
                ];

                return $payload;
            },
            $payload
        );

        return $payload;
    }
}