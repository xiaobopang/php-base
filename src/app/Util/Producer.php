<?php

declare(strict_types=1);

namespace App\Util;

use App\Constants\LoggerConstant;
use Hyperf\Amqp\Message\ProducerMessageInterface;
use Hyperf\Utils\ApplicationContext;

class Producer
{
    public static function produce(ProducerMessageInterface $producerMessage, bool $confirm = false, int $timeout = 5): bool
    {
        $startTime = microtime(true);
        $producer = ApplicationContext::getContainer()->get(\Hyperf\Amqp\Producer::class);

        if ($producer->produce($producerMessage, $confirm, $timeout)) {
            Logger::info('[Producer] 数据发往 MQ 成功', ['time' => microtime(true) - $startTime, 'data' => $producerMessage->payload()], [LoggerConstant::CHANNEL_MQ]);

            return true;
        }

        Logger::error('[Producer] 发送数据到 MQ 失败，请检查 MQ 状态', func_get_args(), [LoggerConstant::CHANNEL_MQ]);

        return false;
    }
}
