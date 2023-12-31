<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller\Http;


use App\Model\User;

class IndexController extends BaseController
{

    public function index()
    {
        $params = $this->request->all() ?: [];
        $rules = [
            'nickname' => 'sometimes|string|max:8',
        ];
        $messages = [
		'nickname.string' => 'nickname类型是字符串',
		'nickname.max'    => 'nickname长度不能超过8',
        ];
        $this->validated($params, $rules, $messages);

        $result = snowFlake();
        if (!empty($params['nickname'])) {
            $result = User::query()->where('id', 2)->first();
        }
        return $this->response->success($result);
    }
}
