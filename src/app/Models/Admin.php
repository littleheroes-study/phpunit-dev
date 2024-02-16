<?php

namespace App\Models;

use PDO;
use App\Models\BaseModel;

class Admin extends BaseModel
{
    /*
    |--------------------------------------------------------------------------
    | 管理者モデル
    |--------------------------------------------------------------------------
    */
    public function findById(int|string $id): array
    {
        $sql = "
            SELECT 
                *
            FROM 
                admins
            WHERE 
                id = :id
            AND 
                deleted_at IS NULL
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $salon = $stmt->fetch(PDO::FETCH_ASSOC);
        return $salon;
    }

    public function create(array $admin): false|int
    {
        $hash_pass = password_hash($admin['password'], PASSWORD_DEFAULT);
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
                    :name,
                    :name_kana,
                    :email,
                    :password
                )
            ";
        $stmt = $this->pdo->prepare($sql);
        $this->pdo->beginTransaction();
        try {
            $stmt->bindValue(':name', $admin['name'], PDO::PARAM_STR);
            $stmt->bindValue(':name_kana', $admin['name_kana'], PDO::PARAM_STR);
            $stmt->bindValue(':email', $admin['email'], PDO::PARAM_STR);
            $stmt->bindValue(':password', $hash_pass, PDO::PARAM_STR);
            $stmt->execute();
            $lastInsertId = $this->pdo->lastInsertId();
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
        return $lastInsertId;
    }

    public function createToken(): string
    {
        $token = '';
        while (true) {
            $token = bin2hex(openssl_random_pseudo_bytes(32));
            $sql = "SELECT id FROM admins WHERE token = :token AND deleted_at IS NULL";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':token', $token, PDO::PARAM_INT);
            $stmt->execute();
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$item) {
                break;
            }
        }
        return $token;
    }

    public function findByLoginId(string $loginId): array|false
    {
        $sql = "
            SELECT 
                *
            FROM 
                admins
            WHERE 
                email = :email
            AND 
                deleted_at IS NULL
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $loginId, PDO::PARAM_STR);
        $stmt->execute();
        $salon = $stmt->fetch(PDO::FETCH_ASSOC);
        return $salon;
    }

    public function saveToken(int $userId, string $token): bool
    {
        $sql = "
            UPDATE 
                admins 
            SET 
                token = :token,
                token_expired_at = NOW() + INTERVAL 7 DAY
            WHERE 
                id = :id
            AND
                deleted_at IS NULL
            ";
        $stmt = $this->pdo->prepare($sql);
        $this->pdo->beginTransaction();
        try {
            $stmt->bindValue(':token', $token, PDO::PARAM_STR);
            $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
            $result = $stmt->execute();
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
        return $result;
    }

    public function findByToken(string $token): array|false
    {
        $sql = "
            SELECT 
                *
            FROM 
                admins
            WHERE 
                token = :token
            AND 
                deleted_at IS NULL
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        return $admin;
    }
}