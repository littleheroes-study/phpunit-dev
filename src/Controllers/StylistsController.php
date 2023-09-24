<?php

namespace App\Controllers;

use App\Models\Stylist;
use App\Commons\JsonResponse;
use App\Core\Request;
use App\Core\Response;
use App\Enums\StatusCode;
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
        // リクエスト
        $salon = new Stylist();
        $salon = $salon->findById($request->getQuery('id'));
        // 404
        return (new JsonResponse)->make(
            config('response.salons.detail'),
            $salon,
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