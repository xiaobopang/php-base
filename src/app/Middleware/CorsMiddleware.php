<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\BusinessException;
use Hyperf\Context\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $origin = isset($request->getHeaders()['origin'][0]) ? $request->getHeaders()['origin'][0] : '';
        // var_dump(" ==== Origin ===== ", $request->getHeaders());
        $this->allowOrigin = [
            'http://localhost',
            'http://127.0.0.1',
            'http://localhost:8081',
            'http://localhost:8082',
            'http://127.0.0.1:8082',
            'http://127.0.0.1:8080',
            'http://127.0.0.1:8888',
            'http://127.0.0.1:3000',
            'http://localhost:1024',
        ];

        if (!empty($origin) && !in_array($origin, $this->allowOrigin)) {
            throw new BusinessException(10403, '非法请求');
        }

        $response = Context::get(ResponseInterface::class);
        $response = $response->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            // Headers 可以根据实际情况进行改写。
            ->withHeader('Access-Control-Allow-Headers', 'DNT,Keep-Alive,User-Agent,Cache-Control,Content-Type,Authorization');

        Context::set(ResponseInterface::class, $response);

        if ($request->getMethod() == 'OPTIONS') {
            return $response;
        }

        return $handler->handle($request);
    }
}