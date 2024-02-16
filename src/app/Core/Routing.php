<?php
namespace App\Core;

use Exception;
use App\Core\Handler;

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

    /**
     * @var string action名
     */
    private $requestMethod;

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
        $this->requestMethod = $this->server['REQUEST_METHOD'];
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
            $this->action = $this->getActionNameFromMethod();
            $this->controller = strpos($this->controller, '?') ? strstr($this->controller, '?', true) : $this->controller;
            if (!$this->isExistsController()) {
                throw new Exception();
            }
            if (!$this->isExistsPathParameter($exploded)) {
                throw new Exception();
            }
            if (!$this->isGetMethodParameter($exploded)) {
                throw new Exception();
            }

        } catch(Exception $e) {
            Handler::exceptionFor404();
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

    /**
     * methodからaction名を取得する
     */
    private function getActionNameFromMethod()
    {
        if ($this->isGet()) return 'detail';
        if ($this->isPost()) return 'create';
        if ($this->isPut()) return 'update';
        if ($this->isDelete()) return 'delete';
    }

    private function isExistsPathParameter(array $exploded)
    {
        $pathParam = 0;
        if (
            $this->isPut() ||
            $this->isDelete()
        ) {
            $pathParam = (empty($exploded[3])) ? 0 : $exploded[3];
        }
        return is_numeric($pathParam);
    }
    
    private function isGetMethodParameter(array $exploded)
    {
        $pathParam = 0;
        if (
            $this->isGet()
        ) {
            $pathParam = (empty($exploded[2])) ? 0 : $exploded[2];
            $pathParam = (strpos($pathParam, '?')) ? strstr($pathParam, '?', true) : $pathParam;
            $this->action = ($pathParam < 1) ? 'index' : 'detail';
        }
        return is_numeric($pathParam);
    }

    /**
     * Request MethodがGETかどうか
     * 
     * @return bool
     */
    public function isGet() : bool
    {
        return ($this->requestMethod === 'GET') ? true : false;
    }

    /**
     * Request MethodがPostかどうか
     * 
     * @return bool
     */
    public function isPost() : bool
    {
        return ($this->requestMethod === 'POST') ? true : false;
    }

    /**
     * Request MethodがPUTかどうか
     * 
     * @return bool
     */
    public function isPut() : bool
    {
        return ($this->requestMethod === 'PUT') ? true : false;
    }

    /**
     * Request MethodがDELETEかどうか
     * 
     * @return bool
     */
    public function isDelete() : bool
    {
        return ($this->requestMethod === 'DELETE') ? true : false;
    }
}
