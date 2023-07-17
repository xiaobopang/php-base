<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @method static string getMessage($code, array $params = [])
 */
#[Constants]
class ErrorCode extends AbstractConstants
{
    /**
     * @Message("未知错误")
     */
    public const UNKNOWN_ERROR = 9999;

    /**
     * 认证相关错误.
     */

    /**
     * @Message("认证失败, Authorization 头不存在")
     */
    public const AUTH_HEADER_NOT_EXIST = 1000;

    /**
     * @Message("认证失败, token 非法")
     */
    public const AUTH_FAILED = 1001;

    /**
     * @Message("权限不足")
     */
    public const NO_PERMISSION = 1002;

    /**
     * @Message("登录失败，请联系管理员")
     */
    public const AUTH_CODE_FAILED = 1003;

    /**
     * 客户端参数错误，ApiException.
     */

    /**
     * @Message("参数错误，请参考 API 文档")
     */
    public const INVALID_PARAMS = 2000;

    /**
     * @Message("没有符合条件的数据")
     */
    public const DATA_NOT_FOUND = 2001;

    /**
     * @Message("已存在相同数据")
     */
    public const SAME_DATA_FOUND = 2002;

    /**
     * 文件相关.
     */

    /**
     * @Message("文件错误")
     */
    public const FILE_ERROR = 3000;

    /**
     * @Message("文件保存错误")
     */
    public const FILE_STORED_ERROR = 3001;

    /**
     * 第三方 API 错误.
     */

    /**
     * 供应商 API 有问题.
     *
     * @Message("服务不可用, 请联系管理员")
     */
    public const SERVICE_UNAVAILABLE = 5000;

    /**
     * 用户中心有误.
     *
     * @Message("用户中心出错, 请联系管理员")
     */
    public const ACCOUNT_ERROR = 5001;

    /**
     * elasticsearch 错误.
     *
     * @Message("内部错误, 请联系管理员")
     */
    public const ELASTICSEARCH_ERROR = 5002;

    /**
     * 系统错误.
     */

    /**
     * 内部参数错误.
     *
     * @Message("内部错误, 请联系管理员")
     */
    public const INTERNAL_PARAMS_ERROR = 6000;

    /**
     * 数据库错误.
     *
     * @Message("内部错误, 请联系管理员")
     */
    public const DB_ERROR = 6001;
}
