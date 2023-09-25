<?php

namespace App\Controllers;

use App\Core\Handler;
use App\Core\Request;
use App\Core\Response;
use App\Models\Stylist;
use App\Enums\StatusCode;
use App\Commons\JsonResponse;
class StylistsController
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
            exit;
        }
        return (new JsonResponse)->make(
            config('response.stylists.detail'),
            $stylist,
            StatusCode::OK,
            false
        );
    }

    public function create()
    {
        echo "create action";
    }

    public function update()
    {
        echo "update action";
    }

    public function delete()
    {
        echo "delete action";
    }
}