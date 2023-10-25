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
    public function testExample(): void
    {
        $this->createSalonlist();
        $expected = "[]";
        $response = $this->execGetRequest('/salons');
        $dataCount = count(json_decode($response->getBody()->getContents(), true));
        $this->assertEquals(StatusCode::OK, $response->getStatusCode());
        $this->assertEquals($expected, $dataCount);
    }

    /**
     * データ無しの確認テスト
     */
    public function testNotData(): void
    {
        $expected = "[]";
        $response = $this->execGetRequest('/salons');
        $data = $response->getBody()->getContents();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expected, $data);
    }
    /**
     * 正常レスポンス
     * データ件数
     * データの形
     * ページネーション
     * 2ページ目のページネーション
     * 3ページ目のページネーション
     */
}
