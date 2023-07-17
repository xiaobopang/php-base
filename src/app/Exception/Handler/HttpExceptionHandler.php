<?php

declare(strict_types=1);

namespace App\Exception\Handler;


use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Exception\HttpException;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class HttpExceptionHandler extends ExceptionHandler
{
    #[Inject]
    protected HttpResponse $httpResponse;


    public function __construct(protected StdoutLoggerInterface $logger)
    {
    }
    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $this->stopPropagation();

        $this->logger->info(sprintf('http请求：%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));

        return $this->httpResponse->json([
            'msg' => $throwable->getMessage(),
            'code' => $throwable->getCode(),
            'data' => null, 'timestamp' => time()
        ])->withStatus(200);
    }

    /**
     * isValid.
     *
     * @author yansongda <me@yansongda.cn>
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof HttpException;
    }
}
