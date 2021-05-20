<?php

declare(strict_types=1);

namespace HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch;

use Elasticsearch\Client;
use HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch\DoctrineHitsIteratorAggregate;
use HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch\HitsIteratorAggregate;
use HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\Engines\ElasticSearchEngine;
use Illuminate\Support\ServiceProvider;
use Laravel\Scout\EngineManager;

class ScoutElasticSearchServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        resolve(EngineManager::class)->extend(ElasticSearchEngine::class, function () {
            $elasticsearch = resolve(Client::class);

            return new ElasticSearchEngine($elasticsearch);
        });
    }

    public function register(): void
    {
        $this->app->bind(HitsIteratorAggregate::class, DoctrineHitsIteratorAggregate::class);
    }
}
