<?php

namespace App\Controllers;

use App\Models\Menu;
use App\Models\Salon;
use App\Core\Request;
use App\Core\Handler;
use App\Enums\StatusCode;
use App\Commons\JsonResponse;
use App\Commons\ValidationRegex;
use App\Controllers\BaseController;

class MenusController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | メニュー管理
    |--------------------------------------------------------------------------
    */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->authentication();
    }

    public function index()
    {
        $menu = new Menu();
        $menus = $menu->getAll();
        return (new JsonResponse)->make(
            config('response.menus.index'),
            $menus,
            StatusCode::OK
        );
    }

    public function detail(Request $request)
    {
        $menu = new Menu();
        $menu = $menu->findById($request->getPathParam('id'));
        if (empty($menu)) {
            Handler::exceptionFor404();
        }
        return (new JsonResponse)->make(
            config('response.menus.detail'),
            $menu,
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
        // 予約期限時間が営業開始時間より後か？
        $this->ensureDeadlineTimeAfterStartTime();
        // 予約期限時間が営業終了時間より前か？
        $this->ensureDeadlineTimeBeforeClosingTime();
        $menu = new Menu();
        $isSuccess = $menu->create($request->getAllPrams());
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
        $menu = new Menu();
        $menuEnsure = $menu->findById($request->getParam('menu_id'));
        if (empty($menuEnsure)) {
            Handler::exceptionFor428();
        }
        // 予約期限時間が営業開始時間より後か？
        $this->ensureDeadlineTimeAfterStartTime();
        // 予約期限時間が営業終了時間より前か？
        $this->ensureDeadlineTimeBeforeClosingTime();
        $isSuccess = $menu->update($request->getAllPrams());
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
        $validator = $this->deleteRequest($request->getParam('menu_id'));
        if (!empty($validator)) {
            Handler::exceptionFor422($validator);
        }
        // メニューの存在確認
        $menu = new Menu();
        $menuEnsure = $menu->findById($request->getParam('menu_id'));
        if (empty($menuEnsure)) {
            Handler::exceptionFor428();
        }
        $isSuccess = $menu->delete($request->getParam('menu_id'));
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
        if ($msg = $validation->textCheck($requestParam['description'])) {
            $msgArray['description'] = $msg;
        }
        if ($msg = $validation->numberCheck($requestParam['operation_time'])) {
            $msgArray['operation_time'] = $msg;
        }
        if ($msg = $validation->timeCheck($requestParam['deadline_time'])) {
            $msgArray['deadline_time'] = $msg;
        }
        if ($msg = $validation->numberCheck($requestParam['amount'])) {
            $msgArray['amount'] = $msg;
        }
        if ($msg = $validation->tinyintBoolCheck($requestParam['is_coupon'])) {
            $msgArray['is_coupon'] = $msg;
        }
        if ($msg = $validation->conditionTypeCheck($requestParam['conditions'])) {
            $msgArray['conditions'] = $msg;
        }
        return $msgArray;
    }

    private function updateRequest(array $requestParam): array
    {
        $msg = NULL;
        $msgArray = [];
        $validation = new ValidationRegex();
        if ($msg = $validation->numberCheck($requestParam['menu_id'])) {
            $msgArray['menu_id'] = $msg;
        }
        if ($msg = $validation->numberCheck($requestParam['salon_id'])) {
            $msgArray['salon_id'] = $msg;
        }
        if ($msg = $validation->nameCheck($requestParam['name'])) {
            $msgArray['name'] = $msg;
        }
        if ($msg = $validation->textCheck($requestParam['description'])) {
            $msgArray['description'] = $msg;
        }
        if ($msg = $validation->numberCheck($requestParam['operation_time'])) {
            $msgArray['operation_time'] = $msg;
        }
        if ($msg = $validation->timeCheck($requestParam['deadline_time'])) {
            $msgArray['deadline_time'] = $msg;
        }
        if ($msg = $validation->numberCheck($requestParam['amount'])) {
            $msgArray['amount'] = $msg;
        }
        if ($msg = $validation->tinyintBoolCheck($requestParam['is_coupon'])) {
            $msgArray['is_coupon'] = $msg;
        }
        if ($msg = $validation->conditionTypeCheck($requestParam['conditions'])) {
            $msgArray['conditions'] = $msg;
        }
        return $msgArray;
    }

    private function deleteRequest(mixed $stylistId): array
    {
        $msg = NULL;
        $msgArray = [];
        $validation = new ValidationRegex();
        if ($msg = $validation->numberCheck($stylistId)) {
            $msgArray['menu_id'] = $msg;
        }
        return $msgArray;
    }
}