<?php
namespace App\Core;

use Exception;

class Routing
{
    /**
     * @var array $_SERVER
     */
    private $server;

    /**
     * @var string controller名
     */
    private $controller;

    /**
     * @var string action名
     */
    private $action;

    public function __construct(array $server)
    {
        $this->server = $server;
        $this->separateUrl();
    }

    /**
     * urlをController名とAction名に切り分ける
     * @throw Exception
     */
    private function separateUrl(): void
    {
        $separate = $this->server['REQUEST_URI'];
        try {
            // document root 指定のurlの場合は404エラーを返す
            if(empty($separate)) throw new Exception();

            // controllerとactionに分ける
            $exploded = array_filter(explode('/', $separate), function($a) {
                return !is_string( $a ) || strlen( $a ) ;
            });
            if((count($exploded) < 1)) {
                throw new Exception();
            }
            
            $this->controller = $this->upperCamelize($exploded[1]);
            $this->action = (!empty($exploded[2])) ? $this->lowerCamelize($exploded[2]) : 'index';
            $this->action = (strpos($this->action, '?')) ? strstr($this->action, '?', true) : $this->action;
            if(!$this->isExistsController())  throw new Exception();

        } catch(Exception $e) {
            echo json_encode(['error' => 404]);
            exit;
        }
    }

    /**
     * スネークケースをアッパーキャメルケースに変換
     */
    private static function upperCamelize(string $str): string
    {
        return ucfirst(strtr(ucwords(strtr($str, ['_' => ' '])), [' ' => '']));
    }

    /**
     * スネークケースをアッパーキャメルケースに変換
     */
    private static function lowerCamelize(string $str): string
    {
        return lcfirst(strtr(ucwords(strtr($str, ['_' => ' '])), [' ' => '']));
    }

    /**
     * Controller名を取得する
     */
    public function getController(): string
    {
        return $this->controller . 'Controller';
    }

    /**
     * action名を取得する
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Controller / actionがあるか調べる
     */
    private function isExistsController(): bool
    {
        return file_exists(dirname(__DIR__) . '/Controllers/'. $this->controller . 'Controller.php');
    }
}
