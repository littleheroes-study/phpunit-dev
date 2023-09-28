<?php
namespace App\Commons;

use DateTime;

class ValidateCheckWithDb
{
	/**
	 * 認証パスワードの確認
	 * @param string $loginPw リクエストされたlogin_pw
	 * @param string $hashLoginPw ユーザーtableに登録されているlogin_pw
	 * @return mixed
	 */
	function passwordVerify($loginPw, $hashLoginPw)
	{
		if (password_verify($loginPw, $hashLoginPw)) {
			return true;
		}
		
		return 'E2001';
	}

	/**
	 * tokenが存在するか確認
	 * @param array $token リクエストヘッダーにリクエストされたtoken
	 * @return mixed trueまたはエラーコード
	 */
	function checkToken($token)
	{
		if (!empty($token)) {
			return true;
		}

		return 'E2002';
	}

	/**
	 * tokenが有効期限内か確認する
	 * @param string $currentTime 現在時刻
	 * @param array $userData usersから取得したTokenの有効期限
	 * @return mixed trueまたはエラーコード
	 */
	function checkTokenExpires($currentTime, $userData)
	{
		$checkToken = $this->checkToken($userData['token']);
		$tokenDate = new DateTime($userData['token_expires_at']);
		if (!is_bool($checkToken)) {
			return $checkToken;
		}
		if ($currentTime <= $tokenDate) {
			return true;
		}
		
		return 'E2003';
	}

	/**
	 * 指定の映画情報を取得出来ているか確認
	 * @param int $movie_id 単品映画情報
	 * @return mixed trueまたはエラーコード
	 */
	function checkMovieInfo($movie_id) 
	{
		if (!empty($movie_id)) {
			return true;
		}

		return 'E2004';
	}

	/**
	 * コメントの編集が出来たか確認
	 * @param int $commentData 編集したcomment_id
	 * @return mixed trueまたはエラーコード
	 */
	function checkCommentEdit($commentData)
	{
		if (!empty($commentData)) {
			return true;
		}
		
		return 'E2005';
	}

	/**
	 * コメントの削除が出来たか確認
	 * @param int $commentData 削除したcomment_id
	 * @return mixed trueまたはエラーコード
	 */
	function checkCommentDelete($commentData)
	{
		if (!empty($commentData)) {
			return true;
		}
		
		return 'E2006';
	}
}
