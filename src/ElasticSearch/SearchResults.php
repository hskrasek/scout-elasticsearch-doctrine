<?php

declare(strict_types=1);

namespace HSkrasek\LaravelDoctrine\Scout\DoctrineElasticSearch\ElasticSearch;

use Illuminate\Contracts\Support\Arrayable;
use IteratorAggregate;

interface SearchResults extends Arrayable, IteratorAggregate
{

}
