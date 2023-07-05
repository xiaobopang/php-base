<?php

declare(strict_types=1);

namespace App\Util;

use App\Constants\LoggerConstant;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * @method static void emergency($message, array $context = [], array $logger = [])
 * @method static void alert($message, array $context = [], array $logger = [])
 * @method static void critical($message, array $context = [], array $logger = [])
 * @method static void error($message, array $context = [], array $logger = [])
 * @method static void warning($message, array $context = [], array $logger = [])
 * @method static void notice($message, array $context = [], array $logger = [])
 * @method static void info($message, array $context = [], array $logger = [])
 * @method static void debug($message, array $context = [], array $logger = [])
 * @method static void log($message, array $context = [], array $logger = [])
 */
class Logger
{
    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function __callStatic(string $method, array $params): void
    {
        $channel = $params[2][0] ?? $params[2]['channel'] ?? LoggerConstant::CHANNEL_APP;
        $config = $params[2][1] ?? $params[2]['config'] ?? 'default';
        $logger = Logger::get($channel, $config);

        if (method_exists($logger, $method)) {
            $logger->{$method}($params[0] ?? '', $params[1] ?? []);
        }
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): LoggerInterface
    {
        return Logger::get('sys', 'system');
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function get(string $name = 'app', string $config = 'default'): LoggerInterface
    {
        return ApplicationContext::getContainer()->get(LoggerFactory::class)->get($name, $config);
    }
}
