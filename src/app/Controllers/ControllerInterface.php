<?php

namespace App\Controllers;

Interface ControllerInterface
{
    public function index();
    public function detail();
    public function create();
    public function update();
    public function delete();
}