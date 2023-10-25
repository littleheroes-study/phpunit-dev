<?php

namespace App\Controllers;

use App\Core\Handler;
use App\Core\Request;
use App\Models\Salon;
use App\Models\Stylist;
use App\Enums\StatusCode;
use App\Commons\JsonResponse;
use App\Commons\ValidationRegex;
use App\Controllers\BaseController;
class StylistsController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | スタイリスト管理
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $stylist = new Stylist();
        $stylists = $stylist->getAll();
        return (new JsonResponse)->make(
            config('response.stylists.index'),
            $stylists,
            StatusCode::OK
        );
    }

    public function detail(Request $request)
    {
        $stylist = new Stylist();
        $stylist = $stylist->findById($request->getQuery('id'));
        if (empty($stylist)) {
            Handler::exceptionFor404();
        }
        return (new JsonResponse)->make(
            config('response.stylists.detail'),
            $stylist,
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
        // サロンの存在確認
        $salon = new Salon();
        $salonEnsure = $salon->findById($request->getParam('salon_id'));
        if (empty($salonEnsure)) {
            Handler::exceptionFor428();
        }
        $stylist = new Stylist();
        $isSuccess = $stylist->create($request->getAllPrams());
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
        // サロンの存在確認
        $salon = new Salon();
        $salonEnsure = $salon->findById($request->getParam('salon_id'));
        if (empty($salonEnsure)) {
            Handler::exceptionFor428();
        }
        // スタイリストの存在確認
        $stylist = new Stylist();
        $stylistEnsure = $stylist->findById($request->getParam('stylist_id'));
        if (empty($stylistEnsure)) {
            Handler::exceptionFor428();
        }
        $isSuccess = $stylist->update($request->getAllPrams());
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
        $validator = $this->deleteRequest($request->getParam('stylist_id'));
        if (!empty($validator)) {
            Handler::exceptionFor422($validator);
        }
        // スタイリストの存在確認
        $stylist = new Stylist();
        $stylistEnsure = $stylist->findById($request->getParam('stylist_id'));
        if (empty($stylistEnsure)) {
            Handler::exceptionFor428();
        }
        $isSuccess = $stylist->delete($request->getParam('stylist_id'));
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
        if ($msg = $validation->numberCheck($requestParam['salon_id'])) {
            $msgArray['salon_id'] = $msg;
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
        if ($msg = $validation->numberCheck($requestParam['appoint_fee'])) {
            $msgArray['appoint_fee'] = $msg;
        }
        if ($msg = $validation->numberCheck($requestParam['stylist_history'])) {
            $msgArray['stylist_history'] = $msg;
        }
        if ($msg = $validation->textCheck($requestParam['skill'])) {
            $msgArray['skill'] = $msg;
        }
        return $msgArray;
    }

    private function updateRequest(array $requestParam): array
    {
        $msg = NULL;
        $msgArray = [];
        $validation = new ValidationRegex();
        if ($msg = $validation->numberCheck($requestParam['stylist_id'])) {
            $msgArray['stylist_id'] = $msg;
        }
        if ($msg = $validation->numberCheck($requestParam['salon_id'])) {
            $msgArray['salon_id'] = $msg;
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
        if ($msg = $validation->numberCheck($requestParam['appoint_fee'])) {
            $msgArray['appoint_fee'] = $msg;
        }
        if ($msg = $validation->numberCheck($requestParam['stylist_history'])) {
            $msgArray['stylist_history'] = $msg;
        }
        if ($msg = $validation->textCheck($requestParam['skill'])) {
            $msgArray['skill'] = $msg;
        }
        return $msgArray;
    }

    private function deleteRequest(mixed $stylistId): array
    {
        $msg = NULL;
        $msgArray = [];
        $validation = new ValidationRegex();
        if ($msg = $validation->numberCheck($stylistId)) {
            $msgArray['stylist_id'] = $msg;
        }
        return $msgArray;
    }
}