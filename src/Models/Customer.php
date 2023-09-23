<?php

namespace App\Models;

use App\Models\BaseModel;

class Customer extends BaseModel
{
    /*
    |--------------------------------------------------------------------------
    | 会員モデル
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