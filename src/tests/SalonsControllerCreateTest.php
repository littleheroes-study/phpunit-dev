<?php
namespace Tests;

use App\Enums\StatusCode;
use Tests\BaseTestCase as TestCase;

class SalonsControllerCreateTest extends TestCase 
{
    /*
    |--------------------------------------------------------------------------
    | SalonsController Create Action Test
    |--------------------------------------------------------------------------
    */
    private function createBaseOptions()
    {
        $baseOptions = [
            'name' => 'テストサロン',
            'description' => 'サロンの〜〜〜説明文',
            'zipcode' => '1234567',
            'address' => '東京都杉並区田中XXXX-XXXXX',
            'phone_number' => '0000000000',
            'start_time' => '09:00:00',
            'closing_time' => '21:00:00',
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
    public function testCreateSuccess() {
        $baseOptions = $this->createBaseOptions();
        $expected = ['id' => 1];
        $expected = json_encode($expected);
        $response = $this->execPostRequest('/salons', $baseOptions);
        $this->assertEquals(StatusCode::CREATED, $response->getStatusCode());
        $response = $response->getBody()->getContents();
        $this->assertEquals($expected, $response);
    }

    /**
     * 名前テスト
     * @dataProvider providerNameCheck
     */
    public function testValidationName($expected, $testData) {
        $baseOptions = $this->createBaseOptions();
        $baseOptions['name'] = $testData;
        try {
            $response = $this->execPostRequest('/salons', $baseOptions);
            $this->assertEquals($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertEquals($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * Nameの文字種文字長テストのためのデータを返す
     */
    public static function providerNameCheck(): array
    {
        return [
            [StatusCode::CREATED, 'あああああああ'],    // 正常系
            [StatusCode::UNPROCESSABLE_ENTITY, NULL], // NULL
            [StatusCode::UNPROCESSABLE_ENTITY, ''], // 空文字
            [StatusCode::CREATED, 'あああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ'],    // 境界チェック - 255文字
            [StatusCode::UNPROCESSABLE_ENTITY, 'ああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ'],    // 境界チェック - 256文字
        ];
    }

    /**
     * サロン説明テスト
     * @dataProvider providerDescriptionCheck
     */
    public function testValidationDescription($expected, $testData) {
        $baseOptions = $this->createBaseOptions();
        $baseOptions['description'] = $testData;
        try {
            $response = $this->execPostRequest('/salons', $baseOptions);
            $this->assertEquals($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertEquals($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * descriptionの文字種文字長テストのためのデータを返す
     */
    public static function providerDescriptionCheck(): array
    {
        return [
            [StatusCode::CREATED, 'ああああああああああ'],    // 正常系
            [StatusCode::CREATED, str_repeat('あ', 16383)], // 境界チェック - 65535文字
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
        $baseOptions = $this->createBaseOptions();
        $baseOptions['zipcode'] = $testData;
        try {
            $response = $this->execPostRequest('/salons', $baseOptions);
            $this->assertEquals($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertEquals($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * zipcodeの文字種文字長テストのためのデータを返す
     */
    public static function providerZipcodeCheck(): array
    {
        return [
            [StatusCode::CREATED, '0000000'],    // 正常系
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
        $baseOptions = $this->createBaseOptions();
        $baseOptions['address'] = $testData;
        try {
            $response = $this->execPostRequest('/salons', $baseOptions);
            $this->assertEquals($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertEquals($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * addressの文字種文字長テストのためのデータを返す
     */
    public static function providerAddressCheck(): array
    {
        return [
            [StatusCode::CREATED, 'ああああああああああ'],    // 正常系
            [StatusCode::CREATED, str_repeat('あ', 16383)], // 境界チェック - 65535文字
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
        $baseOptions = $this->createBaseOptions();
        $baseOptions['phone_number'] = $testData;
        try {
            $response = $this->execPostRequest('/salons', $baseOptions);
            $this->assertEquals($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertEquals($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * phone_numberの文字種文字長テストのためのデータを返す
     */
    public static function providerPhoneNumberCheck(): array
    {
        return [
            [StatusCode::CREATED, '00000000000'],    // 正常系
            [StatusCode::UNPROCESSABLE_ENTITY, '000000000'], // 境界チェック - 9文字
            [StatusCode::CREATED, '0000000000'], // 境界チェック - 10文字
            [StatusCode::CREATED, '0000000000000'], // 境界チェック - 13文字
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
        $baseOptions = $this->createBaseOptions();
        $baseOptions['start_time'] = $testData;
        try {
            $response = $this->execPostRequest('/salons', $baseOptions);
            $this->assertEquals($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertEquals($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * start_timeの文字種文字長テストのためのデータを返す
     */
    public static function providerStartTimeCheck(): array
    {
        return [
            [StatusCode::CREATED, '09:59:59'],    // 正常系
            [StatusCode::UNPROCESSABLE_ENTITY, '09:00:000'],    // フォーマット崩れ
            [StatusCode::UNPROCESSABLE_ENTITY, '09:00'],    // フォーマット崩れ
            [StatusCode::UNPROCESSABLE_ENTITY, '09-00-00'],    // 指定外の区切り文字
            [StatusCode::UNPROCESSABLE_ENTITY, '24:01:00'],    // 存在しない時刻
            [StatusCode::UNPROCESSABLE_ENTITY, '25:00:00'],    // 存在しない時刻
            [StatusCode::UNPROCESSABLE_ENTITY, '00:60:00'],    // 存在しない時刻
            [StatusCode::UNPROCESSABLE_ENTITY, '00:00:60'],    // 存在しない時刻
            [StatusCode::UNPROCESSABLE_ENTITY, NULL], // NULLが含まれている
            [StatusCode::UNPROCESSABLE_ENTITY, ''], // 空文字
            [StatusCode::UNPROCESSABLE_ENTITY, '22:00:00'], // 閉店時間よりも未来
            [StatusCode::UNPROCESSABLE_ENTITY, '21:00:00'], // 閉店時間と同じ
        ];
    }

    /**
     * 閉店時間テスト
     * @dataProvider providerClosingTimeCheck
     */
    public function testValidationClosingTime($expected, $testData) {
        $baseOptions = $this->createBaseOptions();
        $baseOptions['closing_time'] = $testData;
        try {
            $response = $this->execPostRequest('/salons', $baseOptions);
            $this->assertEquals($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertEquals($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * closing_timeの文字種文字長テストのためのデータを返す
     */
    public static function providerClosingTimeCheck(): array
    {
        return [
            [StatusCode::CREATED, '09:59:59'],    // 正常系
            [StatusCode::UNPROCESSABLE_ENTITY, '09:00:000'],    // フォーマット崩れ
            [StatusCode::UNPROCESSABLE_ENTITY, '09:00'],    // フォーマット崩れ
            [StatusCode::UNPROCESSABLE_ENTITY, '09-00-00'],    // 指定外の区切り文字
            [StatusCode::UNPROCESSABLE_ENTITY, '24:01:00'],    // 存在しない時刻
            [StatusCode::UNPROCESSABLE_ENTITY, '25:00:00'],    // 存在しない時刻
            [StatusCode::UNPROCESSABLE_ENTITY, '00:60:00'],    // 存在しない時刻
            [StatusCode::UNPROCESSABLE_ENTITY, '00:00:60'],    // 存在しない時刻
            [StatusCode::UNPROCESSABLE_ENTITY, NULL], // NULLが含まれている
            [StatusCode::UNPROCESSABLE_ENTITY, ''], // 空文字
            [StatusCode::UNPROCESSABLE_ENTITY, '08:00:00'], // 開店時間よりも過去
            [StatusCode::UNPROCESSABLE_ENTITY, '09:00:00'], // 開店時間と同じ
        ];
    }

    /**
     * 定休日テスト
     * @dataProvider providerHolidayCheck
     */
    public function testValidationHoliday($expected, $testData) {
        $baseOptions = $this->createBaseOptions();
        $baseOptions['holiday'] = $testData;
        try {
            $response = $this->execPostRequest('/salons', $baseOptions);
            $this->assertEquals($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertEquals($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * holidayの文字種文字長テストのためのデータを返す
     */
    public static function providerHolidayCheck(): array
    {
        return [
            [ // 正常系
                StatusCode::CREATED,
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
                StatusCode::CREATED,
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
                StatusCode::CREATED,
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
        $baseOptions = $this->createBaseOptions();
        $baseOptions['payment_methods'] = $testData;
        try {
            $response = $this->execPostRequest('/salons', $baseOptions);
            $this->assertEquals($expected, $response->getStatusCode());
        } catch (\Exception $e) {
            $this->assertEquals($expected, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * payment_methodsの文字種文字長テストのためのデータを返す
     */
    public static function providerPaymentMethodsCheck(): array
    {
        return [
            [ // 正常系
                StatusCode::CREATED,
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
                StatusCode::CREATED,
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
                StatusCode::CREATED,
                [],
            ],
            [ // 配列ではない
                StatusCode::UNPROCESSABLE_ENTITY,
                'Cash',
            ],
        ];
    }
}

