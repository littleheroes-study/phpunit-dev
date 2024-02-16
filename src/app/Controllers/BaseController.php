<?php

namespace App\Controllers;

use DateTime;
use App\Core\Request;
use App\Models\Admin;
use App\Core\Handler;

class BaseController
{
    /*
    |--------------------------------------------------------------------------
    | コントローラ基底クラス
    |--------------------------------------------------------------------------
    */
    protected $request;

    protected $authUser = null;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function authentication()
    {
        // ヘッダーにAuthorizationが含まれているかどうか
        $header = $this->request->getRequestHeaders();
        if (empty($header['Authorization'])) {
            Handler::exceptionFor401();
        }
        $this->authUser = (new Admin)->findByToken($header['Authorization']);
        if (empty($this->authUser) && $this->authUser) {
            Handler::exceptionFor401();
        }
        $now = new DateTime();
        $tokenExpiredAt = new DateTime($this->authUser['token_expired_at']);
        if ($now > $tokenExpiredAt) {
            Handler::exceptionFor401();
        }
        return;
    }
}