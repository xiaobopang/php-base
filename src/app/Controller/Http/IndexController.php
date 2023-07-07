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
            'nickname' => 'sometimes|string|max:5',
        ];
        $messages = [
            'nickname.string' => 'user类型是字符串',
        ];
        $this->validated($params, $rules, $messages);

        $result = snowFlake();
        if (!empty($params['nickname'])) {

            var_dump(1111111);
            $result = User::query()->where('id', 2)->first();
            var_dump(22222);
            var_dump($result);
        }
        return $this->response->success($result);
    }
}
