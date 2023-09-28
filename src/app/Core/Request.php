<?php
namespace App\Core;

class Request
{
    /**
     * @var string
     */
    private $requestMethod;

    /**
     * getの場合のquery
     * 
     * @var array
     */
    private $queries;

    /**
     * post/put/deleteの場合のparameters
     */
    public $parameters;

    /**
     * コンストラクター
     */
    public function __construct()
    {
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->setQueriesOrParams();
    }

    /**
     * queries / parameters をset
     */
    private function setQueriesOrParams()
    {
        if($this->isGet()) $this->setQueries();
        if($this->isPost()) $this->setPostParameters();
        if($this->isPut() || $this->isDelete()) $this->setPutOrDeleteParameters();
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


    /**
     * GETの場合パラメーターセット。
     */
    private function setQueries()
    {
        $gets = $_GET;
        $this->queries = $gets;
    }

    /**
     * POSTの場合のパラーメータセット。
     */
    private function setPostParameters()
    {
        $post = [];
        if($_SERVER['CONTENT_TYPE'] === 'application/json')  {
            $post = json_decode(file_get_contents('php://input'), true);
        }
        $this->parameters = $post;
    }

    /**
     * PUT, DELETEの場合パラメーターセット
     */
    private function setPutOrDeleteParameters()
    {
        $params = parse_str(file_get_contents('php://input'));
        if(empty($params) && $_SERVER['CONTENT_TYPE'] === 'application/json')  {
            $params = json_decode(file_get_contents('php://input'), true);
        }
        $this->parameters = $params;
    }

    /**
     * 単一のqueryを取得
     */
    public function getQuery($key)
    {
        return $this->queries[$key] ?? NULL;
    }

    /**
     * 単一のparameterを取得
     * 
     * @param string $key
     */
    public function getParam($key)
    {
        return $this->parameters[$key];
    }

    /**
     * 全てのqueryを取得
     */
    public function getAllQueries()
    {
        return $this->queries;
    }

    /**
     * 全てのparameterを取得
     */
    public function getAllPrams()
    {
        return $this->parameters;
    }

    /**
     * 全てのリクエストヘッダーを取得
     */
    public function getRequestHeaders()
    {
        $request = getallheaders();
        return $request;
    }

    /**
     * メソッドを取得
     */
    public function getMethod()
    {
        return $this->requestMethod;
    }
}
