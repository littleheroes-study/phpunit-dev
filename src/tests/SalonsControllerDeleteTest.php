<?php
namespace Tests;

use App\Enums\StatusCode;
use Tests\BaseTestCase as TestCase;

class SalonsControllerDeleteTest extends TestCase 
{
    /*
    |--------------------------------------------------------------------------
    | SalonsController Delete Action Test
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
        $this->authenticated();
        $response = $this->execDeleteRequest('/salons/' . $salonId);
        $this->assertSame(StatusCode::NO_CONTENT, $response->getStatusCode());
    }

    /**
     * 存在しないデータの削除テスト
     */
    public function testDeleteFailure() {
        $salonId = 999999;
        $this->authenticated();
        try {
            $response = $this->execDeleteRequest('/salons/' . $salonId);
        } catch (\Exception $e) {
            $this->assertSame(StatusCode::NOT_FOUND, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * 存在しないデータへの削除テスト - 文字列
     */
    public function testDeletePathParamFailure() {
        $salonId = 'hogehoge';
        $this->authenticated();
        try {
            $response = $this->execDeleteRequest('/salons/' . $salonId);
        } catch (\Exception $e) {
            $this->assertSame(StatusCode::NOT_FOUND, $e->getResponse()->getStatusCode());
        }
    }

    /**
     * 削除済みデータへの削除テスト
     */
    public function testDeleteDataFailure() {
        $salonId = $this->createSalon();
        $sql = "UPDATE salons SET deleted_at=now() WHERE id={$salonId}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $deleteSalonId =  (int) $this->pdo->lastInsertId();
        $this->authenticated();
        try {
            $response = $this->execDeleteRequest('/salons/' . $deleteSalonId);
        } catch (\Exception $e) {
            $this->assertSame(StatusCode::NOT_FOUND, $e->getResponse()->getStatusCode());
        }
    }
}

