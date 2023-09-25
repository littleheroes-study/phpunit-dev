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
        $sql = "SELECT * FROM salons WHERE deleted_at IS NULL";
        $stmt = $this->pdo->query($sql);
        $salons = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $salons;
    }

    public function findById(int|string $id): array
    {
        $sql = "SELECT * FROM salons WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $salon = $stmt->fetch(PDO::FETCH_ASSOC);
        return $salon;
    }

    public function create(array $salon): false|int
    {
        $sql = "INSERT INTO salons (name,description,zipcode,address,phone_number,start_time,closing_time,holiday,payment_methods) VALUES (:name,:description,:zipcode,:address,:phone_number,:start_time,:closing_time,:holiday,:payment_methods)";
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
}