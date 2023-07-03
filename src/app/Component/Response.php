<?php

declare (strict_types = 1);

namespace App\Component;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Exception\HttpException;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpServer\Exception\Http\EncodingException;
use Hyperf\HttpServer\Response as HyperfResponse;
use Hyperf\Utils\Codec\Json;
use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;


class Response extends HyperfResponse
{
    /**
     * @Inject
     * @var ResponseInterface
     */
    protected ?PsrResponseInterface $response;

    /**
     * 调用responseInterface方法.
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments): PsrResponseInterface
    {
        return $this->response->{$name}(...$arguments);
    }

    /**
     * success响应  方便以后扩展.
     * @param mixed $data
     */
    public function success($data = null, int $code = 200, string $msg = 'success'): PsrResponseInterface
    {
        $data = [
            'code'      => $code,
            'msg'       => $msg,
            'timestamp' => time(),
            'data'      => $data,
        ];

        return $this->json($data);
    }

    /**
     * error响应  方便以后扩展.
     */
    public function error(int $code = 10400, string $msg = 'failed', $data = null)
    {
        $data = [
            'code'      => $code,
            'msg'       => $msg,
            'timestamp' => time(),
            'data'      => $data,
        ];

        return $this->json($data);
    }
    /**
     * @param array|\Hyperf\Utils\Contracts\Arrayable|\Hyperf\Utils\Contracts\Jsonable $result
     *
     * @param int                                                                      $statusCode
     *
     * @param int                                                                      $options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function json($result, int $statusCode = 200, $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES): PsrResponseInterface
    {
        $data = $this->toJson($result);
        return $this->getResponse()
            ->withStatus($statusCode)
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream($data));
    }

    /**
     * @param array|\Hyperf\Utils\Contracts\Arrayable|\Hyperf\Utils\Contracts\Jsonable $data
     * @param int                                                                      $options
     *
     * @return string
     */
    protected function toJson($data, $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES): string
    {
        try {
            $result = Json::encode($data, $options);
        } catch (\Throwable $exception) {
            throw new EncodingException($exception->getMessage(), $exception->getCode());
        }

        return $result;
    }

    /**
     * @param string $xml
     * @param int    $statusCode
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function toWechatXML(string $xml, int $statusCode = 200): PsrResponseInterface
    {
        return $this->getResponse()
            ->withStatus($statusCode)
            ->withAddedHeader('Content-Type', 'application/xml; charset=utf-8')
            ->withBody(new SwooleStream($xml));
    }

    /**
     * @param string $text plain
     * @param int    $statusCode
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function toTextPlain(string $data, int $statusCode = 200): PsrResponseInterface
    {
        return $this->getResponse()
            ->withStatus($statusCode)
            ->withAddedHeader('Content-Type', 'text/plain; charset=utf-8')
            ->withBody(new SwooleStream($data));
    }

    public function handleException(HttpException $throwable): PsrResponseInterface
    {
        return $this->response()
            ->withAddedHeader('Server', 'Ry')
            ->withStatus($throwable->getStatusCode())
            ->withBody(new SwooleStream($throwable->getMessage()));
    }

    public function response(): PsrResponseInterface
    {
        return Context::get(PsrResponseInterface::class);
    }

}
