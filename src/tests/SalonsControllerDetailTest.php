<?php
namespace Tests;

use App\Enums\StatusCode;
use Tests\BaseTestCase as TestCase;

class SalonsControllerDetailTest extends TestCase 
{
    /*
    |--------------------------------------------------------------------------
    | SalonsController Detail Action Test
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

    /**
     * 正常系確認テスト
     */
    public function testDeleteSuccess() {
        $salonId = $this->createSalon();
        $expected = [
            'id' => $salonId,
            'name' => 'テストサロン',
            'description' => 'テストサロンの説明',
            'zipcode' => '0000000',
            'address' => 'サンプル住所',
            'phone_number' => '000-0000-0000',
            'start_time' => '09:00:00',
            'closing_time' => '21:00:00',
            'holiday' => '2',
            'payment_methods' => 'Cash'
        ];
        $this->authenticated();
        $response = $this->execGetRequest('/salons/' . $salonId);
        $this->assertSame(StatusCode::OK, $response->getStatusCode());
        $this->assertSame($expected, json_decode($response->getBody()->getContents(), true));
    }

    /**
     * 存在しないデータの取得テスト
     */
    public function testDeleteFailure() {
        $salonId = 999999;
        $this->authenticated();
        try {
            $response = $this->execGetRequest('/salons/' . $salonId);
        } catch (\Exception $e) {
            $this->assertSame(StatusCode::NOT_FOUND, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * 存在しないデータの取得テスト - 文字列
     */
    public function testDeletePathParamFailure() {
        $salonId = 'hogehoge';
        $this->authenticated();
        try {
            $response = $this->execGetRequest('/salons/' . $salonId);
        } catch (\Exception $e) {
            $this->assertSame(StatusCode::NOT_FOUND, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * 削除済みデータの取得テスト
     * @doesNotPerformAssertions
     */
    public function testDeleteDataFailure() {
        $salonId = $this->createSalon();
        $sql = "UPDATE salons SET deleted_at=now() WHERE id={$salonId}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $deleteSalonId =  (int) $this->pdo->lastInsertId();
        $this->authenticated();
        try {
            $response = $this->execGetRequest('/salons/' . $deleteSalonId);
        } catch (\Exception $e) {
            $this->assertSame(StatusCode::NOT_FOUND, $e->getResponse()->getStatusCode());
        }
    }
}
