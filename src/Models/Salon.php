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
}