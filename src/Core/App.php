<?php
namespace App\Core;

use Exception;
use App\Core\Request;

class App
{
    /**
     * Controllerのnamespace
     */
    const CONTROLLER = 'App\Controllers';

    /**
     * @var instance 
     */
    protected $controller;

    /**
     * action名
     * 
     * @var string
     */
    protected $action;

    public function __construct(string $controller, string $action)
    {
        $this->setController($controller);
        $this->action = $action;
    }

    /**
     * Controller読み込み(汎用的なuse文)
     */   
    private function setController($controller): void
    {
        $callController = '\\' . self::CONTROLLER .'\\' . $controller;
        $this->controller = new $callController();
    }

    /**
     * Action実行
     */
    public function executeAction(Request $request)
    {
        $action = $this->action;
        return $this->controller->$action($request);
    }
}
