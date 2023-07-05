<?php
declare (strict_types = 1);

namespace App\Middleware;

use App\Component\Response;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Contracts\Arrayable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CoreMiddleware extends \Hyperf\HttpServer\CoreMiddleware
{
    #[Inject]
    protected Response $response;


    protected function handleNotFound(ServerRequestInterface $request): mixed
    {
        // 重写路由找不到的处理逻辑
        // return $this->response()->withStatus(404);
        $result = [
            'code'      => 10404,
            'msg'       => '请求方法不存在',
            'timestamp' => time(),
            'data'      => [
                // $request->getMethod(),
                // $request->getUri()->getPath(),
                // $request->getUri()->getQuery(),
                // $request->getQueryParams(),
                // $request->getParsedBody(),
                // $request->getRequestTarget()
            ],
        ];
        return $this->response->json($result)->withStatus(404);
    }


    protected function handleMethodNotAllowed(array $methods, ServerRequestInterface $request): mixed
    {
        // 重写 HTTP 方法不允许的处理逻辑
        // return $this->response()->withStatus(405);
        $result = [
            'code'      => 10405,
            'msg'       => '请求方式不正确',
            'timestamp' => time(),
            'data'      => null,
        ];
        return $this->response->json($result)->withStatus(405);
    }
}
