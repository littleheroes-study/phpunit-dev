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

    public function findById(int $id): array|bool
    {
        $sql = 
            "SELECT 
                stylists.id,
                stylists.salon_id,
                salons.name as salon_name,
                stylists.name,
                stylists.name_kana,
                stylists.gender,
                stylists.appoint_fee,
                stylists.stylist_history,
                stylists.skill
            FROM stylists
            INNER JOIN salons
            ON salons.id=stylists.salon_id
            WHERE stylists.id=:id
            AND stylists.deleted_at IS NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stylist = $stmt->fetch(PDO::FETCH_ASSOC);
        return $stylist;
    }
}
