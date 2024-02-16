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
     */
    protected $action;

    /**
     * request名
     */
    protected $request;

    public function __construct(string $controller, string $action, Request $request)
    {
        $this->request = $request;
        $this->action = $action;
        $this->setController($controller);
    }

    /**
     * Controller読み込み(汎用的なuse文)
     */   
    private function setController($controller): void
    {
        $callController = '\\' . self::CONTROLLER .'\\' . $controller;
        $this->controller = new $callController($this->request);
    }

    /**
     * Action実行
     */
    public function executeAction()
    {
        $action = $this->action;
        return $this->controller->$action();
    }
}
