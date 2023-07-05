<?php

declare(strict_types=1);

namespace App\Util;

use Hyperf\Context\Context as HContext;

/**
 * @method static mixed get(string $id, $default = null, $coroutineId = null)
 * @method static mixed getOrSet(string $id, $value)
 * @method static mixed set(string $id, $value)
 * @method static bool  has(string $id, $coroutineId = null)
 */
class Context
{
    public static function __callStatic(string $method, array $params): mixed
    {
        return HContext::{$method}(...$params);
    }

    public static function append(string $key, mixed $value): void
    {
        $contextValue = HContext::getOrSet($key, []);

        if (is_array($contextValue)) {
            $contextValue[] = $value;

            HContext::set($key, $contextValue);
        }
    }

    public static function increment(string $key, int $step = 1): void
    {
        $contextValue = HContext::getOrSet($key, 0);

        if (is_int($contextValue)) {
            HContext::set($key, $contextValue + $step);
        }
    }
}
