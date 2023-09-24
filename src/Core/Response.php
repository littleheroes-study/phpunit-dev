<?php
namespace App\Core;

class Response
{
    /**
     * 返却する配列
     * 
     * @var array
     */
    private $jsonConvertArray = [];
    private $statusCode = 200;

    public function response(array $keys, array $resource, bool $isCollection, int $statusCode): void
    {
        $this->statusCode = $statusCode;
        if ($isCollection) {
            $this->groupConvert($keys, $resource);
        } else {
            $this->singleConvert($keys, $resource);
        }
    }
    /**
     * 一覧系の加工処理
     */
    public function groupConvert(array $keys, array $array): void
    {
        $templateArray = [];
        foreach($keys as $key) {
            $templateArray[$key] = array_column($array, $key);
        }
        foreach($templateArray as $key => $values) {
            foreach($values as $index => $value) {
                $this->jsonConvertArray[$index][$key] = $value;
            }
        }
        $this->responseJson();
    }

    /**
     * 詳細系の加工処理
     */
    public function singleConvert(array $keys, array $resource): void
    {
        foreach($keys as $key) {
            $this->jsonConvertArray[$key] = isset($resource[$key]) ? $resource[$key] : NULL;
        }
        $this->responseJson();
    }

    /**
     * Json形式を出力
     */
    public function responseJson(): void
    {
        // ステータスコードを出力
        header('Content-Type: application/json; charset=utf-8');
	    http_response_code($this->statusCode);
        echo json_encode($this->jsonConvertArray, JSON_UNESCAPED_UNICODE);
    }
}
