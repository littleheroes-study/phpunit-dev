<?php

namespace App\Controllers;

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