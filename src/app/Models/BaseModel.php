<?php

namespace App\Models;

use \PDO;

use function App\Controllers\config;

abstract class BaseModel
{
    /**
     * @var PDO
     */
    protected $pdo;
    /*
    |--------------------------------------------------------------------------
    | 基底モデルクラス
    |--------------------------------------------------------------------------
    */
    public function __construct()
    {
        $this->pdo = $this->dbConnect();
    }

    private function dbConnect(): PDO
    {
        $host = config('database.connection.host'); // 開発環境のドメイン
        $dbname = config('database.connection.dbname'); // 開発環境のデータベース名
        $user = 
        $pass = config('database.connection.password'); // 開発環境のデータベースに指定したパスワード
        $dns = "mysql:host=" . $host . ";dbname=" . $dbname . ";";
        try
        {
            $pdo = new PDO(
                $dns, 
                config('database.connection.user'), // 開発環境のデータベースのユーザー名 
                $pass, 
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        }
        catch (\PDOException $e)
        {
            echo $e->getMessage();
            exit;
        }
        return $pdo;
    }
}