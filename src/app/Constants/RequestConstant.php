<?php

declare(strict_types=1);

namespace App\Constants;

class RequestConstant
{
    /**
     * 外部请求认证 header.
     */
    public const HEADER_TOKEN_AUTH_HEADER = [
        'Authorization',
    ];

    /**
     * 外部请求认证 cookie.
     */
    public const HEADER_TOKEN_AUTH_COOKIE = [
        'access_token', 'Authorization',
    ];

    /**
     * 内部请求 header 识别.
     */
    public const HEADER_TOKEN_INTERNAL_IDENTITY = 'pang';

    /**
     * 请求 ID.
     */
    public const HEADER_REQUEST_ID = 'x-request-id';

    /**
     * 认证后 request attribute 中存储的字段.
     */
    public const ATTRIBUTE_AUTH = 'oauth';

    /**
     * K8S service.
     */
    public const DOMAIN_K8S_SERVICE = 'pang-app.pang-prod:8080';

    public const DOMAIN_INTERNAL = 'dzjo.cn;';

    /**
     * 等待 produce 的消息 key.
     */
    public const AFTER_DB_TO_PRODUCE = 'after_db_to_produce';
}
