<?php

declare(strict_types=1);

namespace App\Middleware;

use Hyperf\Contract\Arrayable;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CoreMiddleware extends \Hyperf\HttpServer\CoreMiddleware
{
    /**
     * Handle the response when cannot found any routes.
     *
     * @return array|Arrayable|mixed|ResponseInterface|string
     */
    protected function handleNotFound(ServerRequestInterface $request): mixed
    {
        // 重写路由找不到的处理逻辑
        $result = [
            'code' => 10404,
            'msg' => 'The method not found.',
            'timestamp' => time(),
            'data' => [
                // $request->getMethod(),
                // $request->getUri()->getPath(),
                // $request->getUri()->getQuery(),
                // $request->getQueryParams(),
                // $request->getParsedBody(),
                // $request->getRequestTarget()
            ],
        ];
        return $this->response()->json($result)->withStatus(404);
    }

    /**
     * Handle the response when the routes found but doesn't match any available methods.
     *
     * @return array|Arrayable|mixed|ResponseInterface|string
     */
    protected function handleMethodNotAllowed(array $methods, ServerRequestInterface $request): mixed
    {
        // 重写 HTTP 方法不允许的处理逻辑
        $result = [
            'code' => 10405,
            'msg' => 'The method is not allowed.',
            'timestamp' => time(),
            'data' => null,
        ];
        return $this->response()->json($result)->withStatus(405);
    }
}