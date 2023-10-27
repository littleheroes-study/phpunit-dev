<?php
namespace App\Core;

class Response
{
    /**
     * 返却する配列
     * @var array
     */
    private $jsonConvertArray = [];
    protected $responseCode = 200;

    public function response(array $keys, array $resource, bool $isCollection, int $statusCode): void
    {
        $this->responseCode = $statusCode;
        if ($isCollection) {
            $this->groupConvert($keys, $resource);
        } else {
            $this->singleConvert($keys, $resource);
        }
    }

    /**
     * 一覧系の加工処理
     */
    private function groupConvert(array $keys, array $array): void
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
    private function singleConvert(array $keys, array $resource): void
    {
        foreach($keys as $key) {
            $this->jsonConvertArray[$key] = isset($resource[$key]) ? $resource[$key] : NULL;
        }
        $this->responseJson();
    }

    /**
     * Json形式を出力
     */
    private function responseJson(): void
    {
        $this->setResponseCode();
        $this->setHeader();
        echo json_encode($this->jsonConvertArray, JSON_UNESCAPED_UNICODE);
    }

    /**
     * レスポンスヘッダーを設定
     */
    protected function setHeader(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Disposition, Content-Type, Content-Length, Accept-Encoding");
    }

    /**
     * ステータスコードを設定
     */
    protected function setResponseCode(): void
    {
	    http_response_code($this->responseCode);
    }
}
