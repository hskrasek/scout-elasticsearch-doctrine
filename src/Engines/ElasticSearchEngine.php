<?php

declare(strict_types=1);

namespace HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\Engines;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\ServerErrorResponseException;
use Exception;
use HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch\Params\Bulk;
use HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch\Params\Indices\Refresh;
use HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch\Params\Search as SearchParams;
use HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch\SearchFactory;
use HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch\SearchResults;
use Illuminate\Support\Collection;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Search;

class ElasticSearchEngine extends Engine
{
    protected Client $elasticsearch;

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function update($models)
    {
        $params = new Bulk();
        $params->index($models);

        $response = $this->elasticsearch->bulk($params->toArray());

        if (array_key_exists('errors', $response) && $response['errors']) {
            $error = new ServerErrorResponseException(json_encode($response, JSON_PRETTY_PRINT));

            throw new Exception('Bulk update error', $error->getCode(), $error);
        }
    }

    public function delete($models)
    {
        $params = new Bulk();
        $params->delete($models);

        $this->elasticsearch->bulk($params->toArray());
    }

    public function search(Builder $builder)
    {
        return $this->performSearch($builder, []);
    }

    public function paginate(Builder $builder, $perPage, $page)
    {
        return $this->performSearch(
            $builder,
            [
                'from' => ($page - 1) * $perPage,
                'size' => $perPage,
            ]
        );
    }

    public function mapIds($results)
    {
        return collect($results['hits']['hits'])->pluck('_id');
    }

    public function map(Builder $builder, $results, $model)
    {
        // TODO: Create HitsIteratorAggregate
        $hits = app()->makeWith(
            HitsIteratorAggregate::class,
            [
                'results'  => $results,
                'callback' => $builder->queryCallback,
            ]
        );

        return new Collection($hits);
    }

    public function getTotalCount($results)
    {
        return $results['hits']['total']['value'];
    }

    public function flush($model)
    {
        $indexName = $model->searchableAs();
        $exists    = $this->elasticsearch->indices()->exists(['index' => $indexName]);

        if (!$exists) {
            return;
        }

        $body   = (new Search())->addQuery(new MatchAllQuery())->toArray();
        $params = new SearchParams($indexName, $body);

        $this->elasticsearch->deleteByQuery($params->toArray());
        $this->elasticsearch->indices()->refresh((new Refresh($indexName))->toArray());
    }

    /**
     * @param Builder $builder
     * @param array $options
     *
     * @return SearchResults|mixed
     */
    private function performSearch(Builder $builder, array $options = [])
    {
        $searchBody = SearchFactory::create($builder, $options);

        if ($builder->callback) {
            /** @var callable $callback */
            $callback = $builder->callback;

            return $callback($this->elasticsearch, $searchBody);
        }

        $entity    = $builder->model;
        $indexName = $builder->index ?: $entity->searchableAs();

        $params = new SearchParams($indexName, $searchBody->toArray());

        return $this->elasticsearch->search($params->toArray());
    }
}
