<?php
namespace App\Commons;

use App\Core\Request;
use App\Enums\Gender;
use App\Enums\PaymentType;
use App\Enums\LimitedCondition;
use App\Enums\CustomerStatusType;

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
	 * 必須項目のバリデーション
	 */
	private function requiredCheck(?string $value): bool|string 
    {
        if (!isset($value) || empty($value)) {
            return '必須入力して下さい。';
        }
		return false;
	}

    /**
	 * 最大長255文字のバリデーション
	 */
	private function maximumLengthCharacters255Check(string $value): bool|string
    {
        if (mb_strlen($value) > 255) {
            return '255文字以内で入力して下さい。';
          }
		return false;
	}

    /**
	 * 最大長65535文字のバリデーション
	 */
	public function textCheck(string $value): bool|string
    {
        if ((mb_strlen($value) > 65535)) {
            return '65535文字以内で入力して下さい。';
          }
		return false;
	}

    /**
     * 項目：サロン名
	 * 名前のバリデーション
	 */
	public function nameCheck(string $value): bool|string
    {
        if (
            $this->requiredCheck($value) ||
            $this->maximumLengthCharacters255Check($value)
        ) {
            return '名前は255文字以内で入力して下さい。';
        }
        return false;
	}

    /**
     * 項目：サロン詳細、住所
	 * 詳細のバリデーション
	 */
	public function descriptionCheck(string $value): bool|string
    {
        if (
            $this->requiredCheck($value) ||
            $this->textCheck($value)
        ) {
            return '65535文字以内で入力して下さい。';
        }
        return false;
	}

    /**
     * 項目：郵便番号
	 * 詳細のバリデーション
	 */
	public function zipcodeCheck(string $value): bool|string
    {
        if (
            $this->requiredCheck($value) ||
            $this->lengthCharacters7Check($value)
        ) {
            return '郵便番号は半角数字7文字以内で入力して下さい。';
        }
        return false;
	}

    /**
	 * 半角英数字の255文字の正規表現
	 */
	function alphanumericCharacters255Check($value) {
		if (!preg_match('/^([a-zA-Z0-9_-]{1,255})$/', $value)){
			return '半角英数字255文字以内で入力して下さい。';
		}
		
		return 'E1003';
	}

    /**
	 * 最大長11-13文字のバリデーション
	 */
	public function phoneNumberCheck(string $value): bool|string
    {
        if (!preg_match('/^([0-9]{10,13})$/', $value)) {
            return '電話番号は10文字以上、13文字以下で入力して下さい。';
          }
		return false;
	}

    /**
	 * 最大長7文字のバリデーション
	 */
	private function lengthCharacters7Check(string $value): ?bool
    {
        if (mb_strlen($value) !== 7) {
            return true;
          }
		return false;
	}

    /**
	 * 半角英数字の255文字の正規表現
	 */
	function numericCharactersCheck($value) {
		if (!preg_match('/^([0-9]{1,255})$/', $value)){
			return '半角英数字255文字以内で入力して下さい。';
		}
		
		return 'E1003';
	}

    /**
	 * 性別のバリデーション
	 */
	function genderCheck(string $value): ?bool
    {
        if ($value !== Gender::MALE || $value !== Gender::FEMALE) {
            echo '性別は正しく入力して下さい。';
          }
		return 'E1007';
	}

    /**
	 * 会員ステータスのバリデーション
	 */
	function customerStatusCheck(string $value): ?bool
    {
        if (
            $value !== CustomerStatusType::MEMBER ||
            $value !== CustomerStatusType::TEMPORARY
        ) {
            echo '会員ステータスは正しく入力して下さい。';
          }
		return 'E1007';
	}

    /**
	 * email形式のバリデーション
	 */
	function emailFormatCheck(string $value): ?bool
    {
        if (!preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]+$/', $value)) {
			return 'emailは正しいフォーマットで入力して下さい。';
		}
		return 'E1007';
	}

    /**
	 * 番号関連形式のバリデーション
	 */
	function numberFormatCheck(string $value): ?bool
    {
        if (!preg_match('/^[0-9]+$/', $value)) {
			return '数字で入力して下さい。';
		}
		return false;
	}

    /**
	 * パスワード形式のバリデーション
	 */
	function passwordFormatCheck($value) {
		if (!preg_match('/^([a-zA-Z0-9]{8,16})$/', $value)){
			return 'パスワードは8文字以上16文字以内で入力して下さい。';
		}	
		return 'E1003';
	}

    /**
	 * 時間のバリデーション
	 */
	function timeNumberCheck(int $value): ?bool
    {
        if (!($value > 24)) {
            echo '24時間以内で入力して下さい。';
          }
		return 'E1007';
	}

    /**
	 * 時間形式のバリデーション
	 */
	private function timeFormatCheck(string $value): ?bool
    {
        if (!preg_match('/\d{2}\:\d{2}\:\d{2}/', $value)){
            return '時間を入力して下さい。';
        }
		return false;
	}

    /**
     * 項目：時間
	 * 詳細のバリデーション
	 */
	public function timeCheck(string $value): bool|string
    {
        if (
            $this->requiredCheck($value) ||
            $this->timeFormatCheck($value)
        ) {
            return '時間を入力して下さい。';
        }
        return false;
	}

    /**
	 * 99999999以下のバリデーション
	 */
	function amountOfMoneyCheck(string $value): ?bool
    {
        if (!((int) $value <= 99999999)) {
            echo '99999999万円以下で入力して下さい。';
        }
		return 'E1007';
	}

    /**
	 * boolean型のバリデーション
	 */
	function booleanCheck($value): ?bool
    {
        if (!is_bool($value)) {
            echo 'trueもしくはfalseで入力して下さい。';
        }
		return 'E1007';
	}

    /**
	 * 限定条件のバリデーション
	 */
	function limitedConditionsCheck($value): ?bool
    {
        $oClass = new \ReflectionClass(new LimitedCondition);
        var_dump($oClass->getConstants());
        exit;
        if (!is_bool($value)) {
            echo 'trueもしくはfalseで入力して下さい。';
        }
		return 'E1007';
	}

    /**
	 * 定休日のバリデーション
	 */
	public function regularHolidayCheck(array $values): bool|string
    {
        foreach($values as $value) {
            if (!preg_match('/^([0-6])$/', $value)){
                return '定休日は0から6で入力して下さい。';
            }
        }
		return false;
	}

    /**
	 * 支払い方法のバリデーション
	 */
	public function paymentMethodCheck(array $values): bool|string
    {
        $paymentTypeClass = new \ReflectionClass('App\Enums\PaymentType');
        $enums = $paymentTypeClass->getConstants();
        foreach($values as $value) {
            if (!in_array($value, $enums)) {
                return '指定の支払い方法を選択してください。';
            }
        }
		return false;
	}
}
