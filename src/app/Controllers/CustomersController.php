<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Handler;
use App\Models\Customer;
use App\Enums\StatusCode;
use App\Commons\JsonResponse;
use App\Commons\ValidationRegex;
use App\Controllers\BaseController;

class CustomersController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | 会員管理
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $customer = new Customer();
        $customers = $customer->getAll();
        return (new JsonResponse)->make(
            config('response.customers.index'),
            $customers,
            StatusCode::OK
        );
    }

    public function detail(Request $request)
    {
        $customer = new Customer();
        $customer = $customer->findById($request->getQuery('id'));
        if (empty($customer)) {
            Handler::exceptionFor404();
            exit;
        }
        return (new JsonResponse)->make(
            config('response.customers.detail'),
            $customer,
            StatusCode::OK,
            false
        );
    }

    public function create(Request $request)
    {
        $validator = $this->createRequest($request->getAllPrams());
        if (!empty($validator)) {
            Handler::exceptionFor422($validator);
            exit;
        }
        $customer = new Customer();
        $request->parameters['uuid'] = $customer->createUid();
        $request->parameters['password'] = passwordHash($request->getParam('password'));
        $isSuccess = $customer->create($request->getAllPrams());
        if (!$isSuccess) {
            Handler::exceptionFor409();
            exit;
        }
        return (new JsonResponse)->make(
            ['id'],
            ['id' => $isSuccess],
            StatusCode::CREATED,
            false
        );
    }

    public function update(Request $request)
    {
        $validator = $this->updateRequest($request->getAllPrams());
        if (!empty($validator)) {
            Handler::exceptionFor422($validator);
            exit;
        }
        $customer = new Customer();
        // TODO: 認証機能追加後認可処理を追加する
        $isSuccess = $customer->update($request->getAllPrams());
        if (!$isSuccess) {
            Handler::exceptionFor409();
            exit;
        }
        return (new JsonResponse)->make(
            [],
            [],
            StatusCode::NO_CONTENT,
            false
        );
    }

    public function delete(Request $request)
    {
        $validator = $this->deleteRequest($request->getParam('customer_id'));
        if (!empty($validator)) {
            Handler::exceptionFor422($validator);
            exit;
        }
        $customer = new Customer();
        // TODO: 認証機能追加後認可処理を追加する
        $isSuccess = $customer->delete($request->getParam('customer_id'));
        if (!$isSuccess) {
            Handler::exceptionFor409();
            exit;
        }
        return (new JsonResponse)->make(
            [],
            [],
            StatusCode::NO_CONTENT,
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
        if ($msg = $validation->genderCheck($requestParam['gender'])) {
            $msgArray['gender'] = $msg;
        }
        if ($msg = $validation->customerStatusCheck($requestParam['status'])) {
            $msgArray['status'] = $msg;
        }
        if ($msg = $validation->emailCheck($requestParam['email'])) {
            $msgArray['email'] = $msg;
        }
        if ($msg = $validation->passwordCheck($requestParam['password'])) {
            $msgArray['password'] = $msg;
        }
        if ($msg = $validation->zipcodeCheck($requestParam['zipcode'])) {
            $msgArray['zipcode'] = $msg;
        }
        if ($msg = $validation->descriptionCheck($requestParam['address'])) {
            $msgArray['address'] = $msg;
        }
        return $msgArray;
    }

    private function updateRequest(array $requestParam): array
    {
        $msg = NULL;
        $msgArray = [];
        $validation = new ValidationRegex();
        if ($msg = $validation->numberCheck($requestParam['customer_id'])) {
            $msgArray['customer_id'] = $msg;
        }
        if ($msg = $validation->nameCheck($requestParam['name'])) {
            $msgArray['name'] = $msg;
        }
        if ($msg = $validation->nameCheck($requestParam['name_kana'])) {
            $msgArray['name_kana'] = $msg;
        }
        if ($msg = $validation->genderCheck($requestParam['gender'])) {
            $msgArray['gender'] = $msg;
        }
        if ($msg = $validation->customerStatusCheck($requestParam['status'])) {
            $msgArray['status'] = $msg;
        }
        if ($msg = $validation->emailCheck($requestParam['email'])) {
            $msgArray['email'] = $msg;
        }
        if ($msg = $validation->zipcodeCheck($requestParam['zipcode'])) {
            $msgArray['zipcode'] = $msg;
        }
        if ($msg = $validation->descriptionCheck($requestParam['address'])) {
            $msgArray['address'] = $msg;
        }
        return $msgArray;
    }

    private function deleteRequest(mixed $salonId): array
    {
        $msg = NULL;
        $msgArray = [];
        $validation = new ValidationRegex();
        if ($msg = $validation->numberCheck($salonId)) {
            $msgArray['salon_id'] = $msg;
        }
        return $msgArray;
    }
}