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
        $sql = "
            SELECT 
                stylists.id,
                stylists.salon_id,
                salons.name as salon_name,
                stylists.name,
                stylists.name_kana,
                stylists.gender,
                stylists.appoint_fee,
                stylists.stylist_history,
                stylists.skill
            FROM 
                stylists
            INNER JOIN 
                salons
            ON 
                salons.id=stylists.salon_id
            WHERE 
                stylists.id=:id
            AND 
                stylists.deleted_at IS NULL
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stylist = $stmt->fetch(PDO::FETCH_ASSOC);
        return $stylist;
    }

    public function create(array $stylist): false|int
    {
        $sql = "
            INSERT INTO stylists 
                (
                    salon_id,
                    name,
                    name_kana,
                    gender,
                    appoint_fee,
                    stylist_history,
                    skill
                ) 
            VALUES 
                (
                    :salon_id,
                    :name,
                    :name_kana,
                    :gender,
                    :appoint_fee,
                    :stylist_history,
                    :skill
                )
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':salon_id', $stylist['salon_id'], PDO::PARAM_INT);
        $stmt->bindValue(':name', $stylist['name'], PDO::PARAM_STR);
        $stmt->bindValue(':name_kana', $stylist['name_kana'], PDO::PARAM_STR);
        $stmt->bindValue(':gender', $stylist['gender'], PDO::PARAM_STR);
        $stmt->bindValue(':appoint_fee', $stylist['appoint_fee'], PDO::PARAM_INT);
        $stmt->bindValue(':stylist_history', $stylist['stylist_history'], PDO::PARAM_INT);
        $stmt->bindValue(':skill', $stylist['skill'], PDO::PARAM_STR);
        $result = $stmt->execute();
        if ($result) {
            return $this->pdo->lastInsertId();
        }
        return $result;
    }

    public function update(array $stylist): false|int
    {
        $sql = "
            UPDATE 
                stylists 
            SET 
                salon_id = :salon_id,
                name = :name,
                name_kana = :name_kana,
                gender = :gender,
                appoint_fee = :appoint_fee,
                stylist_history = :stylist_history,
                skill = :stylist_history
            WHERE 
                id = :stylist_id
        ";
        $stmt = $this->pdo->prepare($sql);
        $this->pdo->beginTransaction();
        try {
            $stmt->bindValue(':salon_id', $stylist['salon_id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', $stylist['name'], PDO::PARAM_STR);
            $stmt->bindValue(':name_kana', $stylist['name_kana'], PDO::PARAM_STR);
            $stmt->bindValue(':gender', $stylist['gender'], PDO::PARAM_STR);
            $stmt->bindValue(':appoint_fee', $stylist['appoint_fee'], PDO::PARAM_INT);
            $stmt->bindValue(':stylist_history', $stylist['stylist_history'], PDO::PARAM_INT);
            $stmt->bindValue(':skill', $stylist['skill'], PDO::PARAM_STR);
            $stmt->bindValue(':stylist_id', $stylist['stylist_id'], PDO::PARAM_INT);
            $result = $stmt->execute();
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
        return $result;
    }

    public function delete(int $stylistId): bool
    {
        $sql = "
            UPDATE 
                stylists 
            SET 
                deleted_at = now() 
            WHERE 
                id = :id
            ";
        $stmt = $this->pdo->prepare($sql);
        $this->pdo->beginTransaction();
        try {
            $stmt->bindValue(':id', $stylistId, PDO::PARAM_STR);
            $result = $stmt->execute();
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
        return $result;
    }
}
