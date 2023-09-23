<?php
use App\Core\Routing;
use App\Core\Request;
use App\Core\App;

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
| composerのautolaod機能を呼び出す。
|
*/
require __DIR__.'/vendor/autoload.php';

/*
|------------------------------------------------------------------------
| Call the first setting file
|------------------------------------------------------------------------
|
*/
require __DIR__.'/config/setting.php';

/*
|------------------------------------------------------------------------
| Call the Routing Class
|------------------------------------------------------------------------
|
*/
$routing = new Routing($_SERVER);


/*
|------------------------------------------------------------------------
| Call Controller
|------------------------------------------------------------------------
| 
*/
$controller = $routing->getController();
$action     = $routing->getAction();

echo (new App($controller, $action))->executeAction();
// var_dump('完了');