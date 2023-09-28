<?php

namespace App\Controllers;

use App\Core\Request;

class MenusController
{
    /*
    |--------------------------------------------------------------------------
    | メニュー管理
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        echo "index action";
    }

    public function detail()
    {
        echo "detail action";
    }

    public function create(Request $request)
    {
        echo "create action";
    }

    public function update(Request $request)
    {
        echo "update action";
    }

    public function delete(Request $request)
    {
        echo "delete action";
    }
}