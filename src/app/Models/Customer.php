<?php

namespace App\Models;

use PDO;
use App\Models\BaseModel;

class Customer extends BaseModel
{
    /*
    |--------------------------------------------------------------------------
    | 会員モデル
    |--------------------------------------------------------------------------
    */
    public function getAll(): array
    {
        $sql = "
            SELECT 
                *
            FROM 
                customers
            WHERE 
                deleted_at IS NULL
            ";
        $stmt = $this->pdo->query($sql);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $customers;
    }

    public function findById(int|string $id): array
    {
        $sql = "
            SELECT 
                *
            FROM 
                customers
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

    public function create(array $customer): false|int
    {
        $sql = "
            INSERT INTO customers 
                (
                    name,
                    name_kana,
                    gender,
                    uuid,
                    status,
                    email,
                    phone_number,
                    password,
                    zipcode,
                    address
                )
            VALUES 
                (
                    :name,
                    :name_kana,
                    :gender,
                    :uuid,
                    :status,
                    :email,
                    :phone_number,
                    :password,
                    :zipcode,
                    :address
                )
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', $customer['name'], PDO::PARAM_STR);
        $stmt->bindValue(':name_kana', $customer['name_kana'], PDO::PARAM_STR);
        $stmt->bindValue(':gender', $customer['gender'], PDO::PARAM_STR);
        $stmt->bindValue(':uuid', $customer['uuid'], PDO::PARAM_STR);
        $stmt->bindValue(':status', $customer['status'], PDO::PARAM_STR);
        $stmt->bindValue(':email', $customer['email'], PDO::PARAM_STR);
        $stmt->bindValue(':phone_number', $customer['phone_number'], PDO::PARAM_STR);
        $stmt->bindValue(':password', $customer['password'], PDO::PARAM_STR);
        $stmt->bindValue(':zipcode', $customer['zipcode'], PDO::PARAM_STR);
        $stmt->bindValue(':address', $customer['address'], PDO::PARAM_STR);
        $result = $stmt->execute();
        if ($result) {
            return $this->pdo->lastInsertId();
        }
        return $result;
    }

    public function update(array $customer)
    {
        $sql = "
            UPDATE 
                customers 
            SET 
                name = :name,
                name_kana = :name_kana,
                gender = :gender,
                status = :status,
                email = :email,
                phone_number = :phone_number,
                zipcode = :zipcode,
                address = :address
            WHERE 
                id = :customer_id
            ";
        $stmt = $this->pdo->prepare($sql);
        $this->pdo->beginTransaction();
        try {
            $stmt->bindValue(':name', $customer['name'], PDO::PARAM_STR);
            $stmt->bindValue(':name_kana', $customer['name_kana'], PDO::PARAM_STR);
            $stmt->bindValue(':gender', $customer['gender'], PDO::PARAM_STR);
            $stmt->bindValue(':status', $customer['status'], PDO::PARAM_STR);
            $stmt->bindValue(':email', $customer['email'], PDO::PARAM_STR);
            $stmt->bindValue(':phone_number', $customer['phone_number'], PDO::PARAM_STR);
            $stmt->bindValue(':zipcode', $customer['zipcode'], PDO::PARAM_STR);
            $stmt->bindValue(':address', $customer['address'], PDO::PARAM_STR);
            $stmt->bindValue(':customer_id', $customer['customer_id'], PDO::PARAM_STR);
            $result = $stmt->execute();
            $this->pdo->commit();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            exit;
            $this->pdo->rollBack();
            return false;
        }
        return $result;
    }

    public function delete(int $customerId): bool
    {
        $sql = "
            UPDATE 
                customers 
            SET 
                deleted_at = now() 
            WHERE 
                id = :id
            ";
        $stmt = $this->pdo->prepare($sql);
        $this->pdo->beginTransaction();
        try {
            $stmt->bindValue(':id', $customerId, PDO::PARAM_STR);
            $result = $stmt->execute();
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
        return $result;
    }

    public function createUid(): string
    {
        $uuid = '';
        while (true) {
            $uuid = bin2hex(openssl_random_pseudo_bytes(16));
            $sql = "SELECT id FROM customers WHERE uuid = :uuid AND deleted_at IS NULL";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':uuid', $uuid, PDO::PARAM_INT);
            $stmt->execute();
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$item) {
                break;
            }
        }
        return $uuid;
    }
}