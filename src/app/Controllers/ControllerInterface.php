<?php

namespace App\Controllers;

use App\Core\Request;

Interface ControllerInterface
{
    public function index();
    public function detail(Request $request);
    public function create(Request $request);
    public function update(Request $request);
    public function delete(Request $request);
}