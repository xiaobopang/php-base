<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use App\Exception\ApiException;
use App\Exception\BusinessException;
use App\Exception\ValidateException;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;

class AppExceptionHandler extends ExceptionHandler
{
    #[Inject]
    protected HttpResponse $httpResponse;

    public function __construct(protected StdoutLoggerInterface $logger)
    {
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {

        $this->stopPropagation();

        // $this->logger->error($throwable->getTraceAsString());
        $this->logger->info(sprintf('业务：%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
        // 阻止异常冒泡
        //自定义异常处理
        return $this->httpResponse->json([
            'msg' => $throwable->getMessage(),
            'code' => $throwable->getCode(),
            'data' => null,
            'timestamp' => time()
        ])->withStatus(200);

    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof BusinessException;
    }
}
