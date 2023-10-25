<?php
namespace Tests;

use PDO;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class BaseTestCase extends TestCase 
{
    protected $pdo;
    private string $protocol = 'http://'; // プロトコル
    private string $myIP = '192.168.1.143'; // テスト利用時に設定する
    private string $port = '8080'; // Dockerのappコンテナポート番号
    private string $baseUrl;
    /*
    |--------------------------------------------------------------------------
    | BaseTestCase
    |--------------------------------------------------------------------------
    */
    protected function setUp(): void
    {
        $this->baseUrl = $this->protocol . $this->myIP . ':' . $this->port;
        parent::setUp();
        $this->dbConnect();
        $this->cleanUp();
    }

    /**
     * This method is called after each test.
     */
    protected function tearDown(): void
    {
        $this->cleanUp();
        parent::tearDown();
    }

    /**
     * データベースへの接続
     *
     * @return void
     */
    private function dbConnect()
    {
        $host = 'db'; // 開発環境のドメイン
        $dbname = 'mysql_php'; // 開発環境のデータベース名
        $user = 'phper';
        $pass = 'drowssap'; // 開発環境のデータベースに指定したパスワード
        $dns = "mysql:host=" . $host . ";dbname=" . $dbname . ";";
        try {
            $this->pdo = new PDO($dns, $user, $pass);
        } catch (\PDOException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * 利用テーブルのクリーンアップ
     *
     * @return void
     */
    private function cleanUp()
    {
        try {
            // サロンテーブル削除
            $sql = "DELETE FROM salons";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute();
            // シーケンスのリセット
            $sql = "ALTER TABLE salons AUTO_INCREMENT = 1;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
        } catch (\Exception $e) {
            var_dump($e);
        }
    }

    public function execGetRequest(string $path = '') {
        $client = new Client();
        $response = $client->request(
            'GET',
            $this->baseUrl . $path,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]
        );
        return $response;
    }

    public function execPostRequest(string $path, array $options) { 
        $client = new Client();  
        $response = $client->request(
            'POST',
            $this->baseUrl . $path,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($options)
            ]);
        return $response;
    }

    public function execPutRequest(string $path, array $options) { 
        $client = new Client();  
        $response = $client->request(
            'PUT',
            $this->baseUrl . $path,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode($options)
            ]);
        return $response;
    }

    public function execDeleteRequest(string $path) { 
        $client = new Client();  
        $response = $client->request(
            'DELETE',
            $this->baseUrl . $path,
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);
        return $response;
    }
}