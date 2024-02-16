<?php

namespace App\Controllers;

use App\Core\Request;

Interface ControllerInterface
{
    public function index();
    public function detail();
    public function create();
    public function update();
    public function delete();
}