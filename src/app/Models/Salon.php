<?php

namespace App\Models;

use \PDO;
use App\Models\BaseModel;

class Salon extends BaseModel
{
    /*
    |--------------------------------------------------------------------------
    | サロンモデル
    |--------------------------------------------------------------------------
    */
    public function getAll(): array
    {
        $sql = "
            SELECT 
                id,
                name,
                description
            FROM 
                salons 
            WHERE 
                deleted_at IS NULL
            ";
        $stmt = $this->pdo->query($sql);
        $salons = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $salons;
    }

    public function findById(int $id): array|false
    {
        $sql = "
            SELECT
                id,
                name,
                description,
                zipcode,
                address,
                phone_number,
                start_time,
                closing_time,
                holiday,
                payment_methods
            FROM 
                salons
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

    public function create(array $salon): false|int
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
                    :name,
                    :description,
                    :zipcode,
                    :address,
                    :phone_number,
                    :start_time,
                    :closing_time,
                    :holiday,
                    :payment_methods
                )
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', $salon['name'], PDO::PARAM_STR);
        $stmt->bindValue(':description', $salon['description'], PDO::PARAM_STR);
        $stmt->bindValue(':zipcode', $salon['zipcode'], PDO::PARAM_STR);
        $stmt->bindValue(':address', $salon['address'], PDO::PARAM_STR);
        $stmt->bindValue(':phone_number', $salon['phone_number'], PDO::PARAM_STR);
        $stmt->bindValue(':start_time', $salon['start_time'], PDO::PARAM_STR);
        $stmt->bindValue(':closing_time', $salon['closing_time'], PDO::PARAM_STR);
        $stmt->bindValue(':holiday', implode( ",", $salon['holiday']), PDO::PARAM_STR);
        $stmt->bindValue(':payment_methods', implode( ",", $salon['payment_methods']), PDO::PARAM_STR);
        $result = $stmt->execute();
        if ($result) {
            return $this->pdo->lastInsertId();
        }
        return $result;
    }

    public function update(array $salon): false|int
    {
        $sql = "
            UPDATE 
                salons 
            SET 
                name = :name,
                description = :description,
                zipcode = :zipcode,
                address = :address,
                phone_number = :phone_number,
                start_time = :start_time,
                closing_time = :closing_time,
                holiday = :holiday,
                payment_methods = :payment_methods 
            WHERE 
                id = :salon_id
        ";
        $stmt = $this->pdo->prepare($sql);
        $this->pdo->beginTransaction();
        try {
            $stmt->bindValue(':name', $salon['name'], PDO::PARAM_STR);
            $stmt->bindValue(':description', $salon['description'], PDO::PARAM_STR);
            $stmt->bindValue(':zipcode', $salon['zipcode'], PDO::PARAM_STR);
            $stmt->bindValue(':address', $salon['address'], PDO::PARAM_STR);
            $stmt->bindValue(':phone_number', $salon['phone_number'], PDO::PARAM_STR);
            $stmt->bindValue(':start_time', $salon['start_time'], PDO::PARAM_STR);
            $stmt->bindValue(':closing_time', $salon['closing_time'], PDO::PARAM_STR);
            $stmt->bindValue(':holiday', implode( ",", $salon['holiday']), PDO::PARAM_STR);
            $stmt->bindValue(':payment_methods', implode( ",", $salon['payment_methods']), PDO::PARAM_STR);
            $stmt->bindValue(':salon_id', $salon['salon_id'], PDO::PARAM_INT);
            $result = $stmt->execute();
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
        return $result;
    }

    public function delete(int $salonId): bool
    {
        $sql = "
            UPDATE 
                salons 
            SET 
                deleted_at = now() 
            WHERE 
                id = :id
            ";
        $stmt = $this->pdo->prepare($sql);
        $this->pdo->beginTransaction();
        try {
            $stmt->bindValue(':id', $salonId, PDO::PARAM_STR);
            $result = $stmt->execute();
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
        return $result;
    }
}