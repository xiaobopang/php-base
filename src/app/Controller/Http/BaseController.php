<?php

declare (strict_types = 1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Controller\Http;

use App\Constants\Constant;
use App\Controller\AbstractController;
//use App\Exception\ValidateException;
use \Hyperf\Validation\ValidationException;
class BaseController extends AbstractController
{

    //通用验证器
    public function validated($params, $rules, $message = [])
    {
        $validator = $this->validationFactory->make($params, $rules, $message);
        if ($validator->fails()) {
            //$errorMessage = $validator->errors()->first();  $validator->messages()->first()
            throw new ValidationException($validator);
        }
    }
}
