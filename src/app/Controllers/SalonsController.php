<?php

namespace App\Controllers;

use App\Models\Salon;
use App\Core\Request;
use App\Core\Handler;
use App\DTO\FindManager;
use App\Enums\StatusCode;
use App\Commons\JsonResponse;
use App\Commons\ValidationRegex;
use App\Controllers\BaseController;
use App\Controllers\ControllerInterface;

class SalonsController extends BaseController implements ControllerInterface
{
    /*
    |--------------------------------------------------------------------------
    | サロン管理
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $findManager = FindManager::setFindParam($request->getAllQueries());
        $salon = new Salon();
        $salons = $salon->getAll($findManager);
        return (new JsonResponse)->make(
            config('response.salons.index'),
            $salons,
            StatusCode::OK
        );
    }

    public function detail(Request $request)
    {
        $salon = new Salon();
        $salon = $salon->findById($request->getPathParam('id'));
        if (empty($salon)) {
            Handler::exceptionFor404();
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
        }
        $salon = new Salon();
        $isSuccess = $salon->create($request->getAllPrams());
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

    public function update(Request $request)
    {
        $validator = $this->updateRequest($request->getAllPrams());
        if (!empty($validator)) {
            Handler::exceptionFor422($validator);
        }
        $salon = new Salon();
        $result = $salon->findById($request->getPathParam('id'));
        if (empty($result)) {
            Handler::exceptionFor404();
        }
        $request->parameters['salon_id'] = $request->getPathParam('id');
        $salon = new Salon();
        $isSuccess = $salon->update($request->getAllPrams());
        if (!$isSuccess) {
            Handler::exceptionFor409();
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
        $salon = new Salon();
        $result = $salon->findById($request->getPathParam('id'));
        if (empty($result)) {
            Handler::exceptionFor404();
        }
        $isSuccess = $salon->delete($request->getPathParam('id'));
        if (!$isSuccess) {
            Handler::exceptionFor409();
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
        if (empty($msgArray['start_time']) && empty($msgArray['closing_time'])) {
            if ($msg = $validation->ensureTimeCheck($requestParam['start_time'], $requestParam['closing_time'])) {
                $msgArray['start_time'] = $msg;
            }
        }
        if ($msg = $validation->regularHolidayCheck($requestParam['holiday'])) {
            $msgArray['holiday'] = $msg;
        }
        if ($msg = $validation->paymentMethodCheck($requestParam['payment_methods'])) {
            $msgArray['payment_methods'] = $msg;
        }
        return $msgArray;
    }

    private function updateRequest(array $requestParam): array
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
        if (empty($msgArray['start_time']) && empty($msgArray['closing_time'])) {
            if ($msg = $validation->ensureTimeCheck($requestParam['start_time'], $requestParam['closing_time'])) {
                $msgArray['start_time'] = $msg;
            }
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