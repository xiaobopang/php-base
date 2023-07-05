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


class IndexController extends BaseController
{

    public function index()
    {
        $params = $this->request->all() ?: [];
        $rules = [
            'user' => 'required|string|max:5',
        ];
        $messages = [
            'user.required' => 'user必传参数不能为空',
            'user.max' => '长度不能大于5',
        ];
        $this->validated($params, $rules, $messages);

        return $this->response->success(['hello']);
    }
}
