<?php

namespace App\Controllers;

use App\Models\Admin;
use App\Core\Request;
use App\Core\Handler;
use App\Enums\StatusCode;
use App\Commons\JsonResponse;
use App\Commons\ValidationRegex;
use App\Controllers\BaseController;

class LoginController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | ログイン管理
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $validator = $this->createRequest($this->request->getAllPrams());
        if (!empty($validator)) {
            Handler::exceptionFor422($validator);
        }
        $admin = new Admin();
        $authAdmin = $admin->findByLoginId($this->request->getParam('login_id'));
        if (
            !$authAdmin ||
            !password_verify($this->request->getParam('password'), $authAdmin['password'])
        ) {
            Handler::exceptionFor401();
        }
        $newToken = $admin->createToken();
        $isSuccess = $admin->saveToken($authAdmin['id'], $newToken);
        if (!$isSuccess) {
            Handler::exceptionFor409();
        }
        return (new JsonResponse)->make(
            ['token'],
            ['token' => $newToken],
            StatusCode::OK,
            false
        );
    }

    private function createRequest(array $requestParam): array
    {
        $msg = NULL;
        $msgArray = [];
        $validation = new ValidationRegex();
        if ($msg = $validation->emailCheck($requestParam['login_id'])) {
            $msgArray['login_id'] = $msg;
        }
        if ($msg = $validation->passwordCheck($requestParam['password'])) {
            $msgArray['password'] = $msg;
        }
        return $msgArray;
    }
}