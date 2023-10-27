<?php
namespace Tests;

use App\Enums\StatusCode;
use Tests\BaseTestCase as TestCase;

class SalonsControllerIndexTest extends TestCase 
{
    /*
    |--------------------------------------------------------------------------
    | SalonsController Index Action Test
    |--------------------------------------------------------------------------
    */
    /**
     * テストデータを100件作成する
     *
     * @return void
     */
    public function createSalonlist(): void
    {
        $dataCount = 100;
        for ($i = 1; $i <= $dataCount; $i++) {
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
                        'テストサロン{$i}',
                        'テストサロンの説明{$i}',
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
        }
    }

    /**
     * 正常系確認テスト
     */
    public function testIndexSuccess(): void
    {
        $this->createSalonlist();
        $expected = 30;
        $response = $this->execGetRequest('/salons');
        $responseData = json_decode($response->getBody()->getContents(), true);
        $dataCount = count($responseData);
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
        $this->assertEquals($expected, $dataCount); // デフォルト取得件数
        $this->assertEquals('array', gettype($responseData)); // レスポンス型判定
    }

    /**
     * 取得件数確認テスト - 42件
     */
    public function testIndexLimmitOffsetSuccess(): void
    {
        $this->createSalonlist();
        $expected = 42;
        $response = $this->execGetRequest('/salons?take=' . $expected);
        $dataCount = count(json_decode($response->getBody()->getContents(), true));
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
        $this->assertEquals($expected, $dataCount);
    }

    /**
     * 取得件数確認テスト - オフセット指定(データあり)
     */
    public function testIndexOffsetSuccess(): void
    {
        $this->createSalonlist();
        $expected = 10;
        $response = $this->execGetRequest('/salons?skip=90');
        $dataCount = count(json_decode($response->getBody()->getContents(), true));
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
        $this->assertEquals($expected, $dataCount);
    }

    /**
     * 取得件数確認テスト - オフセット指定(データなし)
     */
    public function testIndexNoContentsSuccess(): void
    {
        $this->createSalonlist();
        $expected = 0;
        $response = $this->execGetRequest('/salons?skip=101');
        $dataCount = count(json_decode($response->getBody()->getContents(), true));
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
        $this->assertEquals($expected, $dataCount);
    }

    /**
     * 取得件数確認テスト - 30/100 件表示(2ページ目)
     */
    public function testIndexSecondPageSuccess(): void
    {
        $this->createSalonlist();
        $expected = 31;
        $response = $this->execGetRequest('/salons?skip=30&take=30');
        $responseData = json_decode($response->getBody()->getContents(), true);
        $dataCount = count($responseData);
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
        $this->assertEquals($expected, $responseData[0]['id']);
    }

    /**
     * 取得件数確認テスト - 60/100 件表示(3ページ目)
     */
    public function testIndexThirdPageSuccess(): void
    {
        $this->createSalonlist();
        $expected = 61;
        $response = $this->execGetRequest('/salons?skip=60&take=30');
        $responseData = json_decode($response->getBody()->getContents(), true);
        $dataCount = count($responseData);
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
        $this->assertEquals($expected, $responseData[0]['id']);
    }

    /**
     * 取得件数確認テスト - 90/100 件表示(4ページ目)
     */
    public function testIndexFourthPageSuccess(): void
    {
        $this->createSalonlist();
        $expected = 91;
        $expectedDataCount = 10;
        $response = $this->execGetRequest('/salons?skip=90&take=30');
        $responseData = json_decode($response->getBody()->getContents(), true);
        $dataCount = count($responseData);
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
        $this->assertEquals($expectedDataCount, $dataCount);
        $this->assertEquals($expected, $responseData[0]['id']);
    }

    /**
     * 取得件数確認テスト - 120/100 件表示(5ページ目)
     */
    public function testIndexFifthPageSuccess(): void
    {
        $this->createSalonlist();
        $expected = 0;
        $response = $this->execGetRequest('/salons?skip=120&take=30');
        $responseData = json_decode($response->getBody()->getContents(), true);
        $dataCount = count($responseData);
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
        $this->assertEquals($expected, $dataCount);
    }

    /**
     * 削除データ未取得確認テスト
     */
    public function testIndexDeleteDataSuccess(): void
    {
        $this->createSalonlist();
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
                    payment_methods,
                    deleted_at
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
                    'Cash',
                    now()
                )
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $expected = 100;
        $response = $this->execGetRequest('/salons?take=101');
        $responseData = json_decode($response->getBody()->getContents(), true);
        $dataCount = count($responseData);
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
        $this->assertEquals($expected, $dataCount);
    }

    /**
     * データ無しの確認テスト
     */
    public function testNotDataSuccess(): void
    {
        $expected = 0;
        $response = $this->execGetRequest('/salons');
        $responseData = json_decode($response->getBody()->getContents(), true);
        $dataCount = count($responseData);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expected, $dataCount);
    }

    /**
     * 取得件数確認テスト - オフセット(マイナス数値)
     */
    public function testIndexOffsetMinusSuccess(): void
    {
        $this->createSalonlist();
        $expected = 30;
        $response = $this->execGetRequest('/salons?skip=-1');
        $responseData = json_decode($response->getBody()->getContents(), true);
        $dataCount = count($responseData);
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
        $this->assertEquals(1, $responseData[0]['id']);
        $this->assertEquals($expected, $dataCount);
    }

    /**
     * 取得件数確認テスト - オフセット(数値以外)
     */
    public function testIndexOffsetNonNumericSuccess(): void
    {
        $this->createSalonlist();
        $expected = 30;
        $response = $this->execGetRequest('/salons?skip=aaa');
        $responseData = json_decode($response->getBody()->getContents(), true);
        $dataCount = count($responseData);
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
        $this->assertEquals(1, $responseData[0]['id']);
        $this->assertEquals($expected, $dataCount);
    }

    /**
     * 取得件数確認テスト - オフセット(NULL)
     */
    public function testIndexOffsetNullSuccess(): void
    {
        $this->createSalonlist();
        $expected = 30;
        $response = $this->execGetRequest('/salons?skip=');
        $responseData = json_decode($response->getBody()->getContents(), true);
        $dataCount = count($responseData);
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
        $this->assertEquals(1, $responseData[0]['id']);
        $this->assertEquals($expected, $dataCount);
    }

    /**
     * 取得件数確認テスト - リミット(マイナス数値)
     */
    public function testIndexLimmitMinusSuccess(): void
    {
        $this->createSalonlist();
        $expected = 30;
        $response = $this->execGetRequest('/salons?take=-1');
        $responseData = json_decode($response->getBody()->getContents(), true);
        $dataCount = count($responseData);
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
        $this->assertEquals(1, $responseData[0]['id']);
        $this->assertEquals($expected, $dataCount);
    }

    /**
     * 取得件数確認テスト - Limmit(数値以外)
     */
    public function testIndexLimmitNonNumericSuccess(): void
    {
        $this->createSalonlist();
        $expected = 30;
        $response = $this->execGetRequest('/salons?take=aaa');
        $responseData = json_decode($response->getBody()->getContents(), true);
        $dataCount = count($responseData);
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
        $this->assertEquals(1, $responseData[0]['id']);
        $this->assertEquals($expected, $dataCount);
    }

    /**
     * 取得件数確認テスト - Limmit(NULL)
     */
    public function testIndexLimmitNullSuccess(): void
    {
        $this->createSalonlist();
        $expected = 30;
        $response = $this->execGetRequest('/salons?take=');
        $responseData = json_decode($response->getBody()->getContents(), true);
        $dataCount = count($responseData);
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
        $this->assertEquals(1, $responseData[0]['id']);
        $this->assertEquals($expected, $dataCount);
    }
}
