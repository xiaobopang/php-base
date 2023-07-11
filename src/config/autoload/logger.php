<?php

declare (strict_types = 1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    'default' => [
        'handler'   => [
            'class'       => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/hyperf.log',
                'level'    => Monolog\Logger::DEBUG,
            ],
        ],
        'formatter' => [
            'class'       => Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'format'                => "[%datetime%]%channel%.%level_name%:%message%\n %context%",
                'dateFormat'            => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
            ],
        ],
    ],
    'request' => [ //请求channel  在request中间件生成请求日志
        'handler'   => [
            'class'       => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/request/request.log',
                'level'    => Monolog\Logger::INFO,
            ],
        ],
        'formatter' => [
            'class'       => Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'format'                => "[%datetime%]%channel%:\r\n%context%\r\n",
                'dateFormat'            => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
            ],
        ],
    ],
    'error'   => [ //错误日志
        'handler'   => [
            'class'       => Monolog\Handler\StreamHandler::class,
            'constructor' => [
                'stream' => BASE_PATH . '/runtime/logs/error.log',
                'level'  => Monolog\Logger::ERROR,
            ],
        ],
        'formatter' => [
            'class'       => Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'format'                => "[%datetime%]%level_name%:%message%\n",
                'dateFormat'            => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
            ],
        ],
    ],
    'queue'   => [ //队列日志
        'handler'   => [
            'class'       => Monolog\Handler\StreamHandler::class,
            'constructor' => [
                'stream' => BASE_PATH . '/runtime/logs/queue.log',
                'level'  => Monolog\Logger::DEBUG,
            ],
        ],
        'formatter' => [
            'class'       => Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'format'                => "[%datetime%]%level_name%:%message%\n",
                'dateFormat'            => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
            ],
        ],
    ],
    'pay'     => [
        'handler'   => [
            'class'       => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/pay.log',
                'level'    => Monolog\Logger::DEBUG,
            ],
        ],
        'formatter' => [
            'class'       => Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'format'                => "[%datetime%]%channel%.%level_name%:%message%\n %context%",
                'dateFormat'            => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
            ],
        ],
    ],
];
