<?php
namespace App\Commons;

use App\Core\Response;

use function App\Helpers\config;

class JsonResponse extends Response
{
	private array $responseKeys;
	private array $values;
	private bool $isCollection = false;
	private int $statusCode;
	/*
    |--------------------------------------------------------------------------
    | JsonResponseクラス
    |--------------------------------------------------------------------------
	|リストに記載したカラムでキーを作成しレスポンスする
    */
	public function make(array $responseKeys, array $values, int $statusCode, bool $isCollection = true)
	{
		$this->responseKeys = $responseKeys;
		$this->values = $values;
		$this->isCollection = $isCollection;
		$this->statusCode = $statusCode;
		return $this->makeJson();
	}

	public function makeJson()
	{
		parent::response(
			$this->responseKeys,
			$this->values,
			$this->isCollection,
			$this->statusCode
		);
	}
}
