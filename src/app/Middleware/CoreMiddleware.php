<?php
declare (strict_types=1);

namespace App\Middleware;

use App\Component\Response;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ServerRequestInterface;

class CoreMiddleware extends \Hyperf\HttpServer\CoreMiddleware
{
    #[Inject]
    protected Response $response;

    /**
     * 重写路由找不到的逻辑
     *
     * @param ServerRequestInterface $request
     *
     * @return mixed
     * @author  pangxiaobo <pangxiaobo@vchangyi.com>
     * @date    2023/7/6 10:57
     */
    protected function handleNotFound(ServerRequestInterface $request): mixed
    {
        // 重写路由找不到的处理逻辑
        $result = [
            'code' => 10404,
            'msg' => 'The method is not found.',
            'timestamp' => time(),
            'data' => [],
        ];
        return $this->response->json($result)->withStatus(404);
    }

    /**
     * 重写Http 方法不允许的逻辑
     *
     * @param array $methods
     * @param ServerRequestInterface $request
     *
     * @return mixed
     * @author  pangxiaobo <pangxiaobo@vchangyi.com>
     * @date    2023/7/6 10:58
     */
    protected function handleMethodNotAllowed(array $methods, ServerRequestInterface $request): mixed
    {
        // 重写 HTTP 方法不允许的处理逻辑
        // return $this->response()->withStatus(405);
        $result = [
            'code' => 10405,
            'msg' => 'The method is not allowed.',
            'timestamp' => time(),
            'data' => [],
        ];
        return $this->response->json($result)->withStatus(405);
    }
}

