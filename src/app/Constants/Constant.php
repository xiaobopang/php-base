<?php

declare (strict_types = 1);
/**
 * This file is part of Hyperf.
 *
 * @see     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class Constant extends AbstractConstants
{

    public const DYNAMIC_KEY_LEN = 10;

    /**
     * redis 锁前缀
     */
    const REDIS_LOCK_PREFIX = 'redis:lock:';

    /**
     * 短信验证码前缀
     */
    const SMS_CODE_PREFIX = 'sms_%s:%s';

    const PAGE = 1;
    /**
     * 默认每页条目数
     */
    const PAGE_SIZE = 10;
    /**
     * 默认每页最大条目数
     */
    const PAGE_MAX_SIZE = 100;

}
