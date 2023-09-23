<?php

namespace App\Models;

use \PDO;

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
        $host="db"; // 開発環境のドメイン
        $dbname="mysql_php"; // 開発環境のデータベース名
        $dns = "mysql:host=" . $host . ";dbname=" . $dbname . ";";
        $user="phper"; // 開発環境のデータベースのユーザー名
        $pass="drowssap"; // 開発環境のデータベースに指定したパスワード
        try
        {
            $pdo = new PDO(
                $dns, 
                $user, 
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