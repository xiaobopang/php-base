<?php

declare (strict_types = 1);
namespace App\Model;

use App\Constants\Constant;
use App\Exception\BusinessException;
use App\Model\BaseModel;

class User extends BaseModel
{
    protected ?string $table = 'user';

    /**
     * 需要隐藏的字段
     *
     * @var array
     */
    protected array $hidden = [
        'deleted_at',
    ];
}
