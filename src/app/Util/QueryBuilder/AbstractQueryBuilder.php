<?php

declare(strict_types=1);

namespace App\Util\QueryBuilder;

use Hyperf\Database\Model\Builder;
use Yansongda\Supports\Str;

abstract class AbstractQueryBuilder
{
    protected Builder $builder;

    public static function build(Builder $builder, array $conditions): Builder
    {
        $builder = (new static())->setBuilder($builder);

        foreach ($conditions as $key => $value) {
            $method = 'build'.Str::studly($key);

            if (method_exists($builder, $method)) {
                $builder->{$method}($value);
            }
        }

        return $builder->getBuilder();
    }

    public function getBuilder(): Builder
    {
        return $this->builder;
    }

    public function setBuilder(Builder $builder): AbstractQueryBuilder
    {
        $this->builder = $builder;

        return $this;
    }
}
