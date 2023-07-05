<?php

declare(strict_types=1);

namespace App\Util;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Hyperf\Guzzle\CoroutineHandler;
use Hyperf\Guzzle\HandlerStackFactory;

class Http
{
    public static function createPool(array $options = [], array $poolOptions = []): Client
    {
        return make(Client::class, [
            'config' => array_merge(
                [
                    'handler' => (new HandlerStackFactory())->create(array_merge([
                        'min_connections' => 1,
                        'max_connections' => 30,
                        'wait_timeout' => 3.0,
                        'max_idle_time' => 30,
                    ], $poolOptions)),
                    'connect_timeout' => 1.0,
                    'timeout' => 2.0,
                ],
                $options,
            ),
        ]);
    }

    public static function create(array $options = []): Client
    {
        return make(Client::class, [
            'config' => array_merge(
                [
                    'handler' => HandlerStack::create(new CoroutineHandler()),
                    'connect_timeout' => 1.0,
                    'timeout' => 5.0,
                ],
                $options,
            ),
        ]);
    }
}
