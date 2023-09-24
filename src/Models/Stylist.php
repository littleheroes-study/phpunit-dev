<?php

namespace App\Models;

use \PDO;
use App\Models\BaseModel;

class Stylist extends BaseModel
{
    /*
    |--------------------------------------------------------------------------
    | スタイリストモデル
    |--------------------------------------------------------------------------
    */
    public function getAll(): array
    {
        $sql = "SELECT * FROM stylists WHERE deleted_at IS NULL";
        $stmt = $this->pdo->query($sql);
        $stylists = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $stylists;
    }

    public function findById(int|string $id): array
    {
        $sql = "SELECT * FROM stylists WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stylist = $stmt->fetch(PDO::FETCH_ASSOC);
        return $stylist;
    }
}