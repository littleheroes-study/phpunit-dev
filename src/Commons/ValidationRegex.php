<?php
namespace App\Commons;

use App\Core\Request;

class ValidationRegex
{
	/**
	 * login時の正規表現
	 * @param array リクエストボディーでリクエストされたパラメーター
	 * @return mixed trueまたはエラーコード
	 */
	function loginCheck($params) {
		$resultId = $this->loginIdCheck($params['login_id']);
		$reultPw = $this->loginPwCheck($params['password']);
		if (!is_bool($resultId)) {
			return $resultId;
		}
		if (!is_bool($reultPw)) {
			return $reultPw;
		}

		return true;
	}

	/**
	 * login_idの正規表現
	 * @param int $loginId リクエストボディでリクエストされたlogin_id
	 * @return mixed trueまたはエラーコード
	 */
	function loginIdCheck($loginId) {
		if (preg_match('/^([a-zA-Z0-9]{8,12})$/', $loginId)) {
			return true;
		} 

		return 'E1002';
	}

	/**
	 * login_pwの正規表現
	 * @param int $loginPw リクエストボディでリクエストされたlogin_pw
	 * @return mixed trueまたはエラーコード
	 */
	function loginPwCheck($loginPw) {
		if (preg_match('/^([a-zA-Z0-9_-]{8,16})$/', $loginPw)) {
			return true;
		}
		
		return 'E1003';
	}

	/**
	 * tokenの正規表現
	 * @param string $token リクエストヘッダーでリクエストされたtoken
	 * @return mixed trueまたはエラーコード
	 */
	function tokenCheck($token) {
		if (preg_match('/^([a-zA-Z0-9]{16})$/', $token)) {
			return true;
		}
		
		return 'E1004';
	}

	/**
	 * movie_idの正規表現
	 * @param string $token リクエストボディでリクエストされたmovie_id
	 * @param mixed trueまたはエラーコード
	 */
	function movieIdCheck($movieId) {
		if (preg_match('/^([0-9]{1,})$/', $movieId)) {
			return true;
		}
		
		return 'E1005';
	}

	/**
	 * commentの正規表現
	 * @param string $token リクエストボディでリクエストされたcomment
	 * @param mixed trueまたはエラーコード
	 */
	function commentCheck($comment) {
		if (preg_match('/^.{1,250}$/', $comment)) {
			return true;
		}
		
		return 'E1006';
	}

	/**
	 * comment_idの正規表現
	 * @param string $token リクエストボディでリクエストされたcomment_id
	 * @param mixed trueまたはエラーコード
	 */
	function commentIdCheck($commentId) {
		if (preg_match('/^([0-9]{1,})$/', $commentId)) {
			return true;
		}

		return 'E1007';
	}

	/**
	 * token movie_id の正規表現
	 * @param string $token リクエストヘッダーでリクエストされたtoken
	 * @param string $movieId リクエストボディでリクエストされたmovie_id
	 * @return mixed trueまたはエラーコード
	 */
	function checkTokenAndMovieid($token, $movieId) {
		$resultToken = $this->tokenCheck($token);
		$resultMovieid = $this->movieIdCheck($movieId);
		if (!is_bool($resultToken)) {
			return $resultToken;
		}
		if (!is_bool($resultMovieid)) {
			return $resultMovieid;
		}

		return true;
	}

	/**
	 * token movie_id commentの正規表現
	 * @param string $token リクエストヘッダーでリクエストされたtoken
	 * @param string $requestParams リクエストボディでリクエストされたパラメーター
	 * @return mixed trueまたはエラーコード
	 */
	function checkTokenAndMovieidAndComment($token, $requestParams) {
		$resultTokenMovieid = $this->checkTokenAndMovieid($token, $requestParams['movie_id']);
		$resultComment = $this->commentCheck($requestParams['comment']);
		if (!is_bool($resultTokenMovieid)) {
			return $resultTokenMovieid;
		}
		if (!is_bool($resultComment)) {
			return $resultComment;
		}

		return true;
	}

	/**
	 * token movie_id comment comment_id 正規表現
	 * @param string $token リクエストヘッダーでリクエストされたtoken
	 * @param array $requestParams リクエストボディでリクエストされたパラメーター
	 */
	function checkTokenAndMovieidAndCommentCommentid($token, $requestParams) {
		$resultTokenMovieidcomment = $this->checkTokenAndMovieidAndComment($token, $requestParams);
		$resultCommentid = $this->commentIdCheck($requestParams['comment_id']);
		if (!is_bool($resultTokenMovieidcomment)) {
			return $resultTokenMovieidcomment;
		}
		if (!is_bool($resultCommentid)) {
			return $resultCommentid;
		}

		return true;
	}

	/**
	 * token movie_id comment_id 正規表現
	 * @param string $token リクエストヘッダーでリクエストされたtoken
	 * @param array $requestParams リクエストボディでリクエストされたパラメーター
	 */
	function checkTokenAndMovieidCommentid($token, $requestParams) {
		$resultTokenMovieid = $this->checkTokenAndMovieid($token, $requestParams['movie_id']);
		$resultCommentid = $this->commentIdCheck($requestParams['comment_id']);
		if (!is_bool($resultTokenMovieid)) {
			return $resultTokenMovieid;
		}
		if (!is_bool($resultCommentid)) {
			return $resultCommentid;
		}

		return true;
	}

    /**
	 * comment_idの正規表現
	 * @param string $token リクエストボディでリクエストされたcomment_id
	 * @param mixed trueまたはエラーコード
	 */
	function commentIdCheck($commentId) {
		if (preg_match('/^([0-9]{1,})$/', $commentId)) {
			return true;
		}

		return 'E1007';
	}
}
