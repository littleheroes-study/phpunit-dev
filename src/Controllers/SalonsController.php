<?php

namespace App\Controllers;

use App\Models\Salon;
use App\Commons\JsonResponse;
use App\Core\Request;
use App\Core\Handler;
use App\Core\Response;
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
        var_dump($request->getAllPrams());
        exit;
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