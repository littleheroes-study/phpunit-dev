<?php

namespace App\Controllers;

use DateTime;
use App\Core\Handler;
use App\Core\Request;
use App\Models\Menu;
use App\Models\Stylist;
use App\Models\Customer;
use App\Models\Reservation;
use App\Enums\StatusCode;
use App\Enums\ConditionType;
use App\Commons\JsonResponse;
use App\Commons\ValidationRegex;
use App\Controllers\BaseController;

class ReservationsController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | 予約管理
    |--------------------------------------------------------------------------
    */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->authentication();
    }

    public function index()
    {
        $reservation = new Reservation();
        $reservations = $reservation->getAll();
        return (new JsonResponse)->make(
            config('response.reservations.index'),
            $reservations,
            StatusCode::OK
        );
    }

    public function detail(Request $request)
    {
        $reservation = new Reservation();
        $reservation = $reservation->findById($request->getQuery('id'));
        if (empty($reservation)) {
            Handler::exceptionFor404();
        }
        return (new JsonResponse)->make(
            config('response.reservations.detail'),
            $reservation,
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
        // 会員の存在確認
        $customer = new Customer();
        $customerEnsure = $customer->findById($request->getParam('customer_id'));
        if (empty($customerEnsure)) {
            Handler::exceptionFor428();
        }
        // メニューの存在確認
        $menu = new Menu();
        $menuEnsure = $menu->findById($request->getParam('menu_id'));
        if (empty($menuEnsure)) {
            Handler::exceptionFor428();
        }
        // スタイリストの存在確認
        $stylist = new Stylist();
        $stylistEnsure = $stylist->findById($request->getParam('stylist_id'));
        if (empty($stylistEnsure)) {
            Handler::exceptionFor428();
        }
        $visitAt = new DateTime($request->getParam('visit_at'));

        // 予約時間は適正か否か？
        if (
            $this->ensureVisitAt(
                $visitAt, 
                $menuEnsure['holiday'],
                $menuEnsure['start_time'],
                $menuEnsure['closing_time'],
                $menuEnsure['deadline_time'],
            )
        ) {
            Handler::exceptionFor428();
        }
        // 限定条件は？
        if ($this->ensureConditions($menuEnsure['conditions'], $customerEnsure['gender'])) {
            Handler::exceptionFor428();
        }
        // スタイリストとサロンが紐づいているか？
        if ($stylistEnsure['salon_id'] === $menuEnsure['salon_id']) {
            Handler::exceptionFor428();
        }
        // 他の予約との重複は？
        $operationStartTime = $visitAt->format('Y-m-d H:i:s');
        $operationEndTime = $visitAt->modify('+' . $menuEnsure['operation_time'] . ' hour')->format('Y-m-d H:i:s');
        $reservation = new Reservation();
        $reservationCount = $reservation->findByVisitAt($operationStartTime, $operationEndTime, $menuEnsure['salon_id'], $stylistEnsure['id']);
        if ($reservationCount > 0) {
            Handler::exceptionFor428();
        }
        $request->parameters['visit_start_at'] = $operationStartTime;
        $request->parameters['visit_end_at'] = $operationEndTime;
        unset($request->parameters['visit_at']);
        $isSuccess = $reservation->create($request->getAllPrams());
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
        echo "update action";
    }

    public function delete(Request $request)
    {
        echo "delete action";
    }

    private function createRequest(array $requestParam): array
    {
        $msg = NULL;
        $msgArray = [];
        $validation = new ValidationRegex();
        if ($msg = $validation->numberCheck($requestParam['customer_id'])) {
            $msgArray['customer_id'] = $msg;
        }
        if ($msg = $validation->numberCheck($requestParam['menu_id'])) {
            $msgArray['menu_id'] = $msg;
        }
        if ($msg = $validation->numberCheck($requestParam['stylist_id'])) {
            $msgArray['stylist_id'] = $msg;
        }
        if ($msg = $validation->tinyintBoolCheck($requestParam['is_first'])) {
            $msgArray['is_first'] = $msg;
        }
        if ($msg = $validation->numberCheck($requestParam['total_amount'])) {
            $msgArray['total_amount'] = $msg;
        }
        if ($msg = $validation->timestampCheck($requestParam['visit_at'])) {
            $msgArray['visit_at'] = $msg;
        }
        return $msgArray;
    }

    // 予約期限時間【時】は適正な時刻かか？
    public function ensureVisitAt(
        DateTime $visitAt,
        string $salonHoliday,
        string $salonStartTime,
        string $salonClosingTime,
        string $salonDeadlineTime
    ): bool {
        $holidays = explode(',', $salonHoliday);
        // 定休日かの確認
        if (in_array($visitAt->format("w"), $holidays)) {
            return true;
        }
        // 営業時間内かの確認
        $targetTime = new DateTime($visitAt->format("H:i:s"));
        $startTime = new DateTime($salonStartTime);
        $closingTime = new DateTime($salonClosingTime);
        // 営業開始時間より前か
        if ($targetTime <= $startTime) {
            return true;
        }
        // 営業開始時間より後か
        if ($targetTime >= $closingTime) {
            return true;
        }
        // 予約期限時間より後か
        $deadlineTime = new DateTime($salonDeadlineTime);
        if ($targetTime >= $deadlineTime) {
            return true;
        }
        return false;
    }

    // 限定条件での可否
    private function ensureConditions(string $conditions, $customerGender): bool
    {
        //誰でも可の場合
        if ($conditions === ConditionType::ANYONE) {
            return false;
        }
        if ($conditions === $customerGender) {
            return false;
        }
        return true;
    }

    private function ensureOtherReservations(DateTime $visitAt, int $operationTime, int $salonId, ?int $stylistId): bool
    {
        $operationStartTime = $visitAt->format('Y-m-d H:i:s');
        $operationEndTime = $visitAt->modify('+' . $operationTime . ' hour')->format('Y-m-d H:i:s');
        $reservation = new Reservation();
        $reservation->findByVisitAt($operationStartTime, $operationEndTime, $salonId, $stylistId);
        exit;
    }
}