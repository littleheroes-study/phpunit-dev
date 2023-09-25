<?php

namespace App\Controllers;

use App\Models\Salon;
use App\Commons\JsonResponse;
use App\Core\Request;
use App\Core\Handler;
use App\Core\Response;
use App\Commons\ValidationRegex;
use App\Enums\StatusCode;

class SalonsController
{
    /*
    |--------------------------------------------------------------------------
    | サロン管理
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $salon = new Salon();
        $salons = $salon->getAll();
        return (new JsonResponse)->make(
            config('response.salons.index'),
            $salons,
            StatusCode::OK
        );
    }

    public function detail(Request $request)
    {
        $salon = new Salon();
        $salon = $salon->findById($request->getQuery('id'));
        if (empty($salon)) {
            Handler::exceptionFor404();
            exit;
        }
        return (new JsonResponse)->make(
            config('response.salons.detail'),
            $salon,
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
        $salon = new Salon();
        $isSuccess = $salon->create($request->getAllPrams());
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

    public function update()
    {
        echo "update action";
    }

    public function delete()
    {
        echo "delete action";
    }

    private function createRequest(array $requestParam): array
    {
        $msg = NULL;
        $msgArray = [];
        $validation = new ValidationRegex();
        if ($msg = $validation->nameCheck($requestParam['name'])) {
            $msgArray['name'] = $msg;
        }
        if ($msg = $validation->descriptionCheck($requestParam['description'])) {
            $msgArray['description'] = $msg;
        }
        if ($msg = $validation->zipcodeCheck($requestParam['zipcode'])) {
            $msgArray['zipcode'] = $msg;
        }
        if ($msg = $validation->descriptionCheck($requestParam['address'])) {
            $msgArray['address'] = $msg;
        }
        if ($msg = $validation->phoneNumberCheck($requestParam['phone_number'])) {
            $msgArray['phone_number'] = $msg;
        }
        if ($msg = $validation->timeCheck($requestParam['start_time'])) {
            $msgArray['start_time'] = $msg;
        }
        if ($msg = $validation->timeCheck($requestParam['closing_time'])) {
            $msgArray['closing_time'] = $msg;
        }
        if ($msg = $validation->regularHolidayCheck($requestParam['holiday'])) {
            $msgArray['holiday'] = $msg;
        }
        if ($msg = $validation->paymentMethodCheck($requestParam['payment_methods'])) {
            $msgArray['payment_methods'] = $msg;
        }
        return $msgArray;
    }
}