<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @method static string getMessage($code, array $params = [])
 */
#[Constants]
class LoggerConstant extends AbstractConstants
{
    /**
     * @Message("app 日志")
     */
    public const CHANNEL_APP = 'app';

    /**
     * @Message("app 日志")
     */
    public const CHANNEL_API = 'api';

    /**
     * @Message("mq 日志")
     */
    public const CHANNEL_MQ = 'mq';
}
