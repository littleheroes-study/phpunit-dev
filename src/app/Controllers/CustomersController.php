<?php

namespace App\Controllers;

use App\Core\Request;
use App\Models\Customer;

class CustomersController
{
    /*
    |--------------------------------------------------------------------------
    | 会員管理
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $customer = new Customer();
        $customer->index();
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