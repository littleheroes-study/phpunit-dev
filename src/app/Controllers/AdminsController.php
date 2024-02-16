<?php

namespace App\Controllers;

use App\Models\Admin;
use App\Core\Request;
use App\Core\Handler;
use App\Enums\StatusCode;
use App\Commons\JsonResponse;
use App\Commons\ValidationRegex;
use App\Controllers\BaseController;

class AdminsController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | 管理者管理
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $validator = $this->createRequest($this->request->getAllPrams());
        if (!empty($validator)) {
            Handler::exceptionFor422($validator);
        }
        $admin = new Admin();
        $isSuccess = $admin->create($this->request->getAllPrams());
        if (!$isSuccess) {
            Handler::exceptionFor409();
        }
        return (new JsonResponse)->make(
            ['id'],
            ['id' => $isSuccess],
            StatusCode::CREATED,
            false
        );
    }

    private function createRequest(array $requestParam): array
    {
        $msg = NULL;
        $msgArray = [];
        $validation = new ValidationRegex();
        if ($msg = $validation->nameCheck($requestParam['name'])) {
            $msgArray['name'] = $msg;
        }
        if ($msg = $validation->nameCheck($requestParam['name_kana'])) {
            $msgArray['name_kana'] = $msg;
        }
        if ($msg = $validation->emailCheck($requestParam['email'])) {
            $msgArray['email'] = $msg;
        }
        if ($msg = $validation->passwordCheck($requestParam['password'])) {
            $msgArray['password'] = $msg;
        }
        return $msgArray;
    }
}