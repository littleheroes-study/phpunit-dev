<?php
namespace Tests;

use App\Enums\StatusCode;
use Tests\BaseTestCase as TestCase;

class SalonsControllerUpdateTest extends TestCase 
{
    /*
    |--------------------------------------------------------------------------
    | SalonsController Update Action Test
    |--------------------------------------------------------------------------
    */
    /**
     * テストデータ作成する
     *
     * @return void
     */
    public function createSalon(): int
    {
        $sql = "
            INSERT INTO salons 
                (
                    name,
                    description,
                    zipcode,
                    address,
                    phone_number,
                    start_time,
                    closing_time,
                    holiday,
                    payment_methods
                )
            VALUES 
                (
                    'テストサロン',
                    'テストサロンの説明',
                    '0000000',
                    'サンプル住所',
                    '000-0000-0000',
                    '09:00:00',
                    '21:00:00',
                    '2',
                    'Cash'
                )
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return (int) $this->pdo->lastInsertId();
    }

    private function updateBaseOptions()
    {
        $baseOptions = [
            'name' => 'テストサロン更新',
            'description' => 'サロンの〜〜〜説明文更新',
            'zipcode' => '1234567',
            'address' => '東京都杉並区田中XXXX-00000',
            'phone_number' => '0000000001',
            'start_time' => '10:00:00',
            'closing_time' => '22:00:00',
            'holiday' => [
                '3',
                '6'
            ],
            'payment_methods' => [
                'Cash',
                'Visa',
                'Mastercard',
                'JCB',
                'American Express',
                'PayPay',
                'Suica',
                'Edy'
            ]
        ];
        return $baseOptions;
    }

    /**
     * 正常系確認テスト
     */
    public function testUpdateSuccess() {
        $salonId = $this->createSalon();
        $baseOptions = $this->updateBaseOptions();
        $this->authenticated();
        $response = $this->execPutRequest('/salons/' . $salonId, $baseOptions);
        $this->assertSame(StatusCode::NO_CONTENT, $response->getStatusCode());
    }

    /**
     * 存在しないデータへの更新テスト
     */
    public function testUpdateFailure() {
        $salonId = 999999;
        $baseOptions = $this->updateBaseOptions();
        $this->authenticated();
        try {
            $response = $this->execPutRequest('/salons/' . $salonId, $baseOptions);
        } catch (\Exception $e) {
            $this->assertSame(StatusCode::NOT_FOUND, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * 存在しないデータへの更新テスト - 文字列
     */
    public function testUpdatePathParamFailure() {
        $salonId = 'hogehoge';
        $baseOptions = $this->updateBaseOptions();
        $this->authenticated();
        try {
            $response = $this->execPutRequest('/salons/' . $salonId, $baseOptions);
        } catch (\Exception $e) {
            $this->assertSame(StatusCode::NOT_FOUND, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * 削除済みデータへの更新テスト
     */
    public function testDeleteDataFailure() {
        $salonId = $this->createSalon();
        $sql = "UPDATE salons SET deleted_at=now() WHERE id={$salonId}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $deleteSalonId =  (int) $this->pdo->lastInsertId();
        $baseOptions = $this->updateBaseOptions();
        $this->authenticated();
        try {
            $response = $this->execPutRequest('/salons/' . $deleteSalonId, $baseOptions);
        } catch (\Exception $e) {
            $this->assertSame(StatusCode::NOT_FOUND, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * 名前テスト
     * @dataProvider providerNameCheck
     */
    public function testValidationName($expected, $testData) {
        $salonId = $this->createSalon();
        $baseOptions = $this->updateBaseOptions();
        $baseOptions['name'] = $testData;
        $this->authenticated();
        try {
            $response = $this->execPutRequest('/salons/' . $salonId, $baseOptions);
            $this->assertSame($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertSame($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * Nameの文字種文字長テストのためのデータを返す
     */
    public static function providerNameCheck(): array
    {
        return [
            [StatusCode::NO_CONTENT, 'あああああああ'],    // 正常系
            [StatusCode::UNPROCESSABLE_ENTITY, NULL], // NULL
            [StatusCode::UNPROCESSABLE_ENTITY, ''], // 空文字
            [StatusCode::NO_CONTENT, 'あああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ'],    // 境界チェック - 255文字
            [StatusCode::UNPROCESSABLE_ENTITY, 'ああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ'],    // 境界チェック - 256文字
        ];
    }

    /**
     * サロン説明テスト
     * @dataProvider providerDescriptionCheck
     */
    public function testValidationDescription($expected, $testData) {
        $salonId = $this->createSalon();
        $baseOptions = $this->updateBaseOptions();
        $baseOptions['description'] = $testData;
        $this->authenticated();
        try {
            $response = $this->execPutRequest('/salons/' . $salonId, $baseOptions);
            $this->assertSame($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertSame($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * descriptionの文字種文字長テストのためのデータを返す
     */
    public static function providerDescriptionCheck(): array
    {
        return [
            [StatusCode::NO_CONTENT, 'ああああああああああ'],    // 正常系
            [StatusCode::NO_CONTENT, str_repeat('あ', 16383)], // 境界チェック - 65535文字
            [StatusCode::UNPROCESSABLE_ENTITY, str_repeat('あ', 16384)], // 境界チェック - 65536文字
            [StatusCode::UNPROCESSABLE_ENTITY, NULL], // 日本語が含まれている
            [StatusCode::UNPROCESSABLE_ENTITY, ''], // 空文字
        ];
    }

    /**
     * 郵便番号テスト
     * @dataProvider providerZipcodeCheck
     */
    public function testValidationZipcode($expected, $testData) {
        $salonId = $this->createSalon();
        $baseOptions = $this->updateBaseOptions();
        $baseOptions['zipcode'] = $testData;
        $this->authenticated();
        try {
            $response = $this->execPutRequest('/salons/' . $salonId, $baseOptions);
            $this->assertSame($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertSame($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * zipcodeの文字種文字長テストのためのデータを返す
     */
    public static function providerZipcodeCheck(): array
    {
        return [
            [StatusCode::NO_CONTENT, '0000000'],    // 正常系
            [StatusCode::UNPROCESSABLE_ENTITY, '000000'], // 境界チェック - 6文字
            [StatusCode::UNPROCESSABLE_ENTITY, '00000000'], // 境界チェック - 8文字
            [StatusCode::UNPROCESSABLE_ENTITY, 'aaaaaaa'], // 数字以外
            [StatusCode::UNPROCESSABLE_ENTITY, NULL], // 日本語が含まれている
            [StatusCode::UNPROCESSABLE_ENTITY, ''], // 空文字
        ];
    }

    /**
     * 住所テスト
     * @dataProvider providerAddressCheck
     */
    public function testValidationAddress($expected, $testData) {
        $salonId = $this->createSalon();
        $baseOptions = $this->updateBaseOptions();
        $baseOptions['address'] = $testData;
        $this->authenticated();
        try {
            $response = $this->execPutRequest('/salons/' . $salonId, $baseOptions);
            $this->assertSame($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertSame($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * addressの文字種文字長テストのためのデータを返す
     */
    public static function providerAddressCheck(): array
    {
        return [
            [StatusCode::NO_CONTENT, 'ああああああああああ'],    // 正常系
            [StatusCode::NO_CONTENT, str_repeat('あ', 16383)], // 境界チェック - 65535文字
            [StatusCode::UNPROCESSABLE_ENTITY, str_repeat('あ', 16384)], // 境界チェック - 65536文字
            [StatusCode::UNPROCESSABLE_ENTITY, NULL], // NULL
            [StatusCode::UNPROCESSABLE_ENTITY, ''], // 空文字
        ];
    }

    /**
     * 電話番号テスト
     * @dataProvider providerPhoneNumberCheck
     */
    public function testValidationPhoneNumber($expected, $testData) {
        $salonId = $this->createSalon();
        $baseOptions = $this->updateBaseOptions();
        $baseOptions['phone_number'] = $testData;
        $this->authenticated();
        try {
            $response = $this->execPutRequest('/salons/' . $salonId, $baseOptions);
            $this->assertSame($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertSame($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * phone_numberの文字種文字長テストのためのデータを返す
     */
    public static function providerPhoneNumberCheck(): array
    {
        return [
            [StatusCode::NO_CONTENT, '00000000000'],    // 正常系
            [StatusCode::UNPROCESSABLE_ENTITY, '000000000'], // 境界チェック - 9文字
            [StatusCode::NO_CONTENT, '0000000000'], // 境界チェック - 10文字
            [StatusCode::NO_CONTENT, '0000000000000'], // 境界チェック - 13文字
            [StatusCode::UNPROCESSABLE_ENTITY, '00000000000000'], // 境界チェック - 14文字
            [StatusCode::UNPROCESSABLE_ENTITY, NULL], // NULLが含まれている
            [StatusCode::UNPROCESSABLE_ENTITY, ''], // 空文字
        ];
    }

    /**
     * 開店時間テスト
     * @dataProvider providerStartTimeCheck
     */
    public function testValidationStartTime($expected, $testData) {
        $salonId = $this->createSalon();
        $baseOptions = $this->updateBaseOptions();
        $baseOptions['start_time'] = $testData;
        $this->authenticated();
        try {
            $response = $this->execPutRequest('/salons/' . $salonId, $baseOptions);
            $this->assertSame($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertSame($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * start_timeの文字種文字長テストのためのデータを返す
     */
    public static function providerStartTimeCheck(): array
    {
        return [
            [StatusCode::NO_CONTENT, '09:59:59'],    // 正常系
            [StatusCode::UNPROCESSABLE_ENTITY, '09:00:000'],    // フォーマット崩れ
            [StatusCode::UNPROCESSABLE_ENTITY, '09:00'],    // フォーマット崩れ
            [StatusCode::UNPROCESSABLE_ENTITY, '09-00-00'],    // 指定外の区切り文字
            [StatusCode::UNPROCESSABLE_ENTITY, '24:01:00'],    // 存在しない時刻
            [StatusCode::UNPROCESSABLE_ENTITY, '25:00:00'],    // 存在しない時刻
            [StatusCode::UNPROCESSABLE_ENTITY, '00:60:00'],    // 存在しない時刻
            [StatusCode::UNPROCESSABLE_ENTITY, '00:00:60'],    // 存在しない時刻
            [StatusCode::UNPROCESSABLE_ENTITY, NULL], // NULLが含まれている
            [StatusCode::UNPROCESSABLE_ENTITY, ''], // 空文字
            [StatusCode::UNPROCESSABLE_ENTITY, '23:00:00'], // 閉店時間よりも未来
            [StatusCode::UNPROCESSABLE_ENTITY, '22:00:00'], // 閉店時間と同じ
        ];
    }

    /**
     * 閉店時間テスト
     * @dataProvider providerClosingTimeCheck
     */
    public function testValidationClosingTime($expected, $testData) {
        $salonId = $this->createSalon();
        $baseOptions = $this->updateBaseOptions();
        $baseOptions['closing_time'] = $testData;
        $this->authenticated();
        try {
            $response = $this->execPutRequest('/salons/' . $salonId, $baseOptions);
            $this->assertSame($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertSame($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * closing_timeの文字種文字長テストのためのデータを返す
     */
    public static function providerClosingTimeCheck(): array
    {
        return [
            [StatusCode::NO_CONTENT, '17:59:59'],    // 正常系
            [StatusCode::UNPROCESSABLE_ENTITY, '09:00:000'],    // フォーマット崩れ
            [StatusCode::UNPROCESSABLE_ENTITY, '09:00'],    // フォーマット崩れ
            [StatusCode::UNPROCESSABLE_ENTITY, '09-00-00'],    // 指定外の区切り文字
            [StatusCode::UNPROCESSABLE_ENTITY, '24:01:00'],    // 存在しない時刻
            [StatusCode::UNPROCESSABLE_ENTITY, '25:00:00'],    // 存在しない時刻
            [StatusCode::UNPROCESSABLE_ENTITY, '00:60:00'],    // 存在しない時刻
            [StatusCode::UNPROCESSABLE_ENTITY, '00:00:60'],    // 存在しない時刻
            [StatusCode::UNPROCESSABLE_ENTITY, NULL], // NULLが含まれている
            [StatusCode::UNPROCESSABLE_ENTITY, ''], // 空文字
            [StatusCode::UNPROCESSABLE_ENTITY, '09:00:00'], // 開店時間よりも過去
            [StatusCode::UNPROCESSABLE_ENTITY, '10:00:00'], // 開店時間と同じ
        ];
    }

    /**
     * 定休日テスト
     * @dataProvider providerHolidayCheck
     */
    public function testValidationHoliday($expected, $testData) {
        $salonId = $this->createSalon();
        $baseOptions = $this->updateBaseOptions();
        $baseOptions['holiday'] = $testData;
        $this->authenticated();
        try {
            $response = $this->execPutRequest('/salons/' . $salonId, $baseOptions);
            $this->assertSame($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertSame($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * holidayの文字種文字長テストのためのデータを返す
     */
    public static function providerHolidayCheck(): array
    {
        return [
            [ // 正常系
                StatusCode::NO_CONTENT,
                [
                    '3',
                    '6'
                ],
            ],
            [ // 存在しない曜日
                StatusCode::UNPROCESSABLE_ENTITY,
                [
                    '9'
                ],
            ],
            [ // 存在しない曜日
                StatusCode::UNPROCESSABLE_ENTITY,
                [
                    '-1'
                ],
            ],
            [ // 曜日の重複
                StatusCode::UNPROCESSABLE_ENTITY,
                [
                    '1',
                    '1',
                ],
            ],
            [ // 全曜日
                StatusCode::NO_CONTENT,
                [
                    '0',
                    '1',
                    '2',
                    '3',
                    '4',
                    '5',
                    '6',
                ],
            ],
            [ // 数字以外
                StatusCode::UNPROCESSABLE_ENTITY,
                [
                    'a'
                ],
            ],
            [ // 数字以外
                StatusCode::UNPROCESSABLE_ENTITY,
                [
                    'あ'
                ],
            ],
            [ // 空
                StatusCode::NO_CONTENT,
                [],
            ],
            [ // 配列ではない
                StatusCode::UNPROCESSABLE_ENTITY,
                '1',
            ],
        ];
    }

    /**
     * 支払い方法テスト
     * @dataProvider providerPaymentMethodsCheck
     */
    public function testValidationPaymentMethods($expected, $testData) {
        $salonId = $this->createSalon();
        $baseOptions = $this->updateBaseOptions();
        $baseOptions['payment_methods'] = $testData;
        $this->authenticated();
        try {
            $response = $this->execPutRequest('/salons/' . $salonId, $baseOptions);
            $this->assertSame($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertSame($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * payment_methodsの文字種文字長テストのためのデータを返す
     */
    public static function providerPaymentMethodsCheck(): array
    {
        return [
            [ // 正常系
                StatusCode::NO_CONTENT,
                [
                    'Cash',
                    'Visa'
                ],
            ],
            [ // 存在しない支払い方法
                StatusCode::UNPROCESSABLE_ENTITY,
                [
                    'panta',
                ],
            ],
            [ // 存在しない支払い方法
                StatusCode::UNPROCESSABLE_ENTITY,
                [
                    'aaa'
                ],
            ],
            [ // 存在しない支払い方法
                StatusCode::UNPROCESSABLE_ENTITY,
                [
                    '1111'
                ],
            ],
            [ // 支払い方法の重複
                StatusCode::UNPROCESSABLE_ENTITY,
                [
                    'PayPay',
                    'PayPay',
                ],
            ],
            [ // 全支払い方法
                StatusCode::NO_CONTENT,
                [
                    'Cash',
                    'Visa',
                    'Mastercard',
                    'JCB',
                    'American Express',
                    'PayPay',
                    'Suica',
                    'Edy'
                ],
            ],
            [ // 空
                StatusCode::NO_CONTENT,
                [],
            ],
            [ // 配列ではない
                StatusCode::UNPROCESSABLE_ENTITY,
                'Cash',
            ],
        ];
    }
}

