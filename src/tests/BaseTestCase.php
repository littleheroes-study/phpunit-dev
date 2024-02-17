<?php
namespace Tests;

use PDO;
use App\Core\Config;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class BaseTestCase extends TestCase 
{
    protected $pdo;
    private string $protocol = 'http://'; // プロトコル
    private string $myIP = 'host.docker.internal'; // テスト利用時に設定する
    private string $port = '8080'; // Dockerのappコンテナポート番号
    private string $baseUrl;
    protected $authAdmin;
    protected $token;
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
            // 管理者テーブル削除
            $sql = "DELETE FROM admins";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute();
            // シーケンスのリセット
            $sql = "ALTER TABLE admins AUTO_INCREMENT = 1;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
        } catch (\Exception $e) {
            var_dump($e);
        }
    }

    public function authenticated()
    {
        Config::set_config_directory('/data/Config');
        $password = password_hash('password', PASSWORD_DEFAULT);
        $sql = "
            INSERT INTO admins 
                (
                    name,
                    name_kana,
                    email,
                    password
                )
            VALUES 
                (
                    '管理者1',
                    'カンリシャイチ',
                    'admin1@example.com',
                    '{$password}'
                )
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $adminId = $this->pdo->lastInsertId();

        $sql = "SELECT * FROM admins WHERE id = {$adminId}";
        $stmt = $this->pdo->query($sql);
        $this->authAdmin = $stmt->fetch(PDO::FETCH_ASSOC);
        $client = new Client();
        $response = $client->request(
            'POST',
            $this->baseUrl . '/login',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode([
                    'login_id' => 'admin1@example.com',
                    'password' => 'password'
                ])
            ]
        );
        $responseData = json_decode($response->getBody()->getContents(), true);
        $this->token = $responseData['token'];
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
                    'Authorization' => $this->token,
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
                    'Authorization' => $this->token,
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
                    'Authorization' => $this->token,
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
                    'Authorization' => $this->token,
                ],
            ]);
        return $response;
    }
}