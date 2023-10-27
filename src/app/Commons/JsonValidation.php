<?php
namespace App\Commons;

use App\Core\Request;

class JsonValidation
{
	/**
	 * リクエストのバリデーション
	 * @param array $keys パラメーターのkey
	 * @param array $method $_SERVER['REQUEST_METHOD']
	 * @return bool
	 */
	function checkRequestParams($keys, $method) {
		//コンテンツタイプがapplication/jsonであるか確認
		$contentType = $this->checkContentType();

		//リクエストごとのパラメーターを取得
		switch ($method) {
			case 'GET':
			$param = $_GET;
			break;
			case 'POST':
			$param = json_decode(file_get_contents('php://input'), true);
			break;
			case 'PUT':
			$param = json_decode(file_get_contents('php://input'), true);
			break;
			case 'DELETE':
			$param = json_decode(file_get_contents('php://input'), true);
			break;
		}

		//JSONがdecodeできるか確認
		$decodeCheck = $this->checkDecode($param);

		//指定のparameterが付与されているか確認
		$checkKeys = $this->checkFromKey($param, $keys);

		//content-type, decode, keyのCHECKでエラーが無いか確認
		$result = [$contentType, $decodeCheck, $checkKeys];
		if (!in_array(false, $result)) {
			return true;
		}
		return 'E1001';
	}

	/**
	 * コンテンツタイプがapplication/jsonであるか確認
	 * @return bool
	 */
	function checkContentType () {
		$contentType = $_SERVER['CONTENT_TYPE'];
		if($contentType === 'application/json') {
			return true;
		}
		return false;
	}

	
	/**
	 * JSONがdecodeできるか確認
	 * @param array $param 配列化したパラメーター
	 * @return bool
	 */
	function checkDecode($param) {
		if (is_array($param)) {
			return true;
		}
		return false;
	}
	
	/**
	 * 指定のparameterが付与されているか確認
	 * @param array $param 配列化されたパラメーター
	 * @param array $keys 配列のキー
	 * @return bool
	 */
	function checkFromKey($param, $keys) {
		foreach ($keys as $key) {
			if (isset($param[$key])) {
				return true;
			}
			return false;
		}
	}
}
