<?php
namespace App\Commons;

use DateTime;
use App\Core\Request;
use App\Enums\Gender;
use App\Enums\PaymentType;
use App\Enums\ConditionType;
use App\Enums\CustomerStatusType;
use phpDocumentor\Reflection\Types\Integer;

class ValidationRegex
{
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
        if ((mb_strlen($value) > 16383)) {
            return '16383文字以内で入力して下さい。';
          }
		return false;
	}

    /**
     * 項目：サロン名
	 * 名前のバリデーション
	 */
	public function nameCheck(?string $value): bool|string
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
	public function descriptionCheck(?string $value): bool|string
    {
        if (
            $this->requiredCheck($value) ||
            $this->textCheck($value)
        ) {
            return '16383文字以内で入力して下さい。';
        }
        return false;
	}

    /**
     * 項目：郵便番号
	 * 詳細のバリデーション
	 */
	public function zipcodeCheck(?string $value): bool|string
    {
        if (
            $this->requiredCheck($value) ||
            $this->lengthCharacters7Check($value) ||
			!is_numeric($value)
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
	public function phoneNumberCheck(?string $value): bool|string
    {
        if (
			$this->requiredCheck($value) ||
			!preg_match('/^([0-9]{10,13})$/', $value)
		) {
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
	private function timeFormatCheck(string $value): false|string
    {
        if (!preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $value)){
            return '時間を入力して下さい。';
        }
		return false;
	}

    /**
	 * 時間形式のバリデーション
	 */
	private function timestampFormatCheck(string $value): false|string
    {
        if (!preg_match('/\d{4}\:\d{2}\:\d{2} \d{4}\:\d{2}\:\d{2}/', $value)){
            return '時間を入力して下さい。';
        }
		return false;
	}

    /**
     * 項目：時間
	 * 詳細のバリデーション
	 */
	public function timeCheck(?string $value): false|string
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
     * 項目：timestamp
	 * 詳細のバリデーション
	 */
	public function timestampCheck(string $value): false|string
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
	 * 定休日のバリデーション
	 */
	public function regularHolidayCheck(mixed $values): false|string
    {
		if (
			!is_array($values) ||
			$this->similarCheck($values)
		) {
			return '定休日を正しく入力してください。';
		}
		if (empty($values)) {
			return false;
		}
        foreach($values as $value) {
            if (!preg_match('/^([0-6])$/', $value)) {
                return '定休日を正しく入力してください。';
            }
        }
		return false;
	}

    /**
	 * 支払い方法のバリデーション
	 */
	public function paymentMethodCheck(mixed $values): false|string
    {
		if (
			!is_array($values) ||
			$this->similarCheck($values)
		) {
			return '指定の支払い方法を選択してください。';
		}
		if (empty($values)) {
			return false;
		}
        $PaymentTypeClass = new \ReflectionClass('App\Enums\PaymentType');
        $enums = $PaymentTypeClass->getConstants();
        foreach($values as $value) {
            if (!in_array($value, $enums)) {
                return '指定の支払い方法を選択してください。';
            }
        }
		return false;
	}

    /**
	 * 性別のバリデーション
	 */
	public function genderCheck(string $value): false|string
    {
        $GenderClass = new \ReflectionClass('App\Enums\Gender');
        $enums = $GenderClass->getConstants();
        if (!in_array($value, $enums)) {
            return '指定の性別を選択してください。';
        }
		return false;
	}

    /**
	 * 数値のバリデーション
	 */
	public function numberCheck($value): false|string
    {
        if (
			$this->requiredCheck($value) ||
        	!is_int($value) ||
			!((int) $value <= 4294967295)
		) {
			return '適正な数値を入力して下さい。';
		}
		return false;
	}

	/**
	 * 会員ステータスのバリデーション
	 */
	public function customerStatusCheck(string $value): false|string
    {
		$CustomerStatusClass = new \ReflectionClass('App\Enums\CustomerStatus');
        $enums = $CustomerStatusClass->getConstants();
        if (!in_array($value, $enums)) {
            return '会員ステータスは正しく入力して下さい。';
        }
		return false;
	}

	/**
	 * email形式のバリデーション
	 */
	private function emailFormatCheck(string $value): bool
    {
        if (!preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]+$/', $value)) {
			return true;
		}
		return false;
	}

	/**
	 * emailのバリデーション
	 */
	public function emailCheck(string $value): false|string
	{
		if (
            $this->requiredCheck($value) ||
            $this->maximumLengthCharacters255Check($value) ||
            $this->emailFormatCheck($value)
        ) {
            return 'emailを正しく入力して下さい。';
        }
        return false;
	}

	/**
	 * passwordのバリデーション
	 */
	public function passwordCheck(string $value): false|string
	{
		if (
            $this->requiredCheck($value) ||
            $this->maximumLengthCharacters255Check($value) ||
            $this->passwordFormatCheck($value)
        ) {
            return 'passwordを正しく入力して下さい。';
        }
        return false;
	}

	/**
	 * パスワード形式のバリデーション
	 */
	private function passwordFormatCheck($value) 
	{
		if (!preg_match('/^([a-zA-Z0-9]{8,16})$/', $value)){
			return 'パスワードは8文字以上16文字以内で入力して下さい。';
		}	
		return false;
	}

	/**
	 * tinyint(1)型のバリデーション
	 */
	public function tinyintBoolCheck($value): false|string
    {
        if ($value > 1) {
            return '0か1を正しく入力して下さい。';
        }
        return false;
	}

    /**
	 * 限定条件のバリデーション
	 */
	public function conditionTypeCheck($value): ?bool
    {
		$ConditionTypeClass = new \ReflectionClass('App\Enums\ConditionType');
        $enums = $ConditionTypeClass->getConstants();
		if (!in_array($value, $enums)) {
            return '限定条件は正しく入力して下さい。';
        }
		return false;
	}

	/**
	 * 配列内の重複バリデーション
	 */
	public function similarCheck(array $list): ?bool
	{
		$valueCount = array_count_values($list);
		if (empty($valueCount)) {
			return false;
		}
		$max = max($valueCount);
		if ($max !== 1) {
			return '重複しない値を入力してください';
		}
		return false;
	}

	/**
	 * 開店閉店時間の整合性
	 */
	public function ensureTimeCheck(string $open, string $close): false|string
	{
		$open = new DateTime($open);
		$close = new DateTime($close);
		if ($close <= $open) {
			return '開店時間は閉店時刻以前に設定してください';
		}
		return false;
	}
}
