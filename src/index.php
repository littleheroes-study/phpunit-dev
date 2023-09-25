<?php
use App\Core\Routing;
use App\Core\Request;
use App\Core\Config;
use App\Core\App;

ini_set("display_errors", 1);
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
Config::set_config_directory(__DIR__ . '/Config');

/*
|------------------------------------------------------------------------
| Call the Routing Class
|------------------------------------------------------------------------
|
*/
$routing = new Routing($_SERVER);
$request = new Request();


/*
|------------------------------------------------------------------------
| Call Controller
|------------------------------------------------------------------------
| 
*/
$controller = $routing->getController();
$action     = $routing->getAction();

echo (new App($controller, $action))->executeAction($request);
// var_dump('完了');