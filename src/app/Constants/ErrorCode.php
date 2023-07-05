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
class ErrorCode extends AbstractConstants
{
    /**
     * @Message("业务错误")
     */
    public const ERROR = 10101;

    /**
     * @Message("Success")
     */
    public const SUCCESS = 10200;

    /**
     * @Message("Token authentication does not pass！")
     */
    public const AUTH_ERROR = 10401;

    /**
     * @Message("Server Error！")
     */
    public const SERVER_ERROR = 10500;

    /**
     * @Message("Service Unavailable Or Refused Request ！")
     */
    public const SERVER_RCP_ERROR = 10503;

    /**
     * @Message("Validate Error ！")
     */
    public const VALIDATE_FAIL = 10000;

    /**
     * @Message("登录失败:用户名或密码错误")
     */
    public const LOGIN_FAIL = 10001;

    /**
     * @Message("上传失败")
     */
    public const UPLOAD_FAIL = 20001;

    /**
     * @Message("超出当日最大查看次数")
     */
    public const DAY_MAX_LOOK_TIMES = 30000;

    /**
     * @Message("支付错误")
     */
    public const PAY_ERROR = 40001;


}
