<?php


declare(strict_types=1);

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use App\Constants\RequestConstant;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ValidationExceptionHandler extends ExceptionHandler
{
    #[Inject]
    protected RequestInterface $request;

    #[Inject]
    protected HttpResponse $httpResponse;

    public function __construct(protected StdoutLoggerInterface $logger)
    {
    }

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $this->stopPropagation();

        /** @var \Hyperf\Validation\ValidationException $throwable */
        $body = $throwable->validator->errors()->first();

        //$this->logger->info(sprintf('参数校验：%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));

        //自定义异常处理
        return $this->httpResponse->json(['msg' => $body, 'code' => ErrorCode::INVALID_PARAMS, 'data' => null, 'timestamp' => time()])->withStatus(400);
    }


    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}