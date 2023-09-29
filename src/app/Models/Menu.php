<?php

namespace App\Models;

use \PDO;
use App\Models\BaseModel;

class Menu extends BaseModel
{
    /*
    |--------------------------------------------------------------------------
    | メニューモデル
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
                menus 
            WHERE 
                deleted_at IS NULL
            ";
        $stmt = $this->pdo->query($sql);
        $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $menus;
    }

    public function findById(int $id): array|false
    {
        $sql = "
            SELECT
                menus.id,
                menus.salon_id,
                salons.name as salon_name,
                menus.name,
                menus.description,
                menus.operation_time,
                menus.deadline_time,
                menus.amount,
                menus.is_coupon,
                menus.conditions
            FROM 
                menus
            INNER JOIN 
                salons
            ON 
                salons.id=menus.salon_id
            WHERE 
                menus.id=:id
            AND 
                menus.deleted_at IS NULL
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $menu = $stmt->fetch(PDO::FETCH_ASSOC);
        return $menu;
    }

    public function create(array $menu): false|int
    {
        $sql = "
            INSERT INTO menus 
                (
                    salon_id,
                    name,
                    description,
                    operation_time,
                    deadline_time,
                    amount,
                    is_coupon,
                    conditions
                )
            VALUES 
                (
                    :salon_id,
                    :name,
                    :description,
                    :operation_time,
                    :deadline_time,
                    :amount,
                    :is_coupon,
                    :conditions
                )
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':salon_id', $menu['salon_id'], PDO::PARAM_INT);
        $stmt->bindValue(':name', $menu['name'], PDO::PARAM_STR);
        $stmt->bindValue(':description', $menu['description'], PDO::PARAM_STR);
        $stmt->bindValue(':operation_time', $menu['operation_time'], PDO::PARAM_INT);
        $stmt->bindValue(':deadline_time', $menu['deadline_time'], PDO::PARAM_STR);
        $stmt->bindValue(':amount', $menu['amount'], PDO::PARAM_STR);
        $stmt->bindValue(':is_coupon', $menu['is_coupon'], PDO::PARAM_INT);
        $stmt->bindValue(':conditions', $menu['conditions'], PDO::PARAM_STR);
        $result = $stmt->execute();
        if ($result) {
            return $this->pdo->lastInsertId();
        }
        return $result;
    }

    public function update(array $menu): false|int
    {
        $sql = "
            UPDATE 
                menus 
            SET 
                salon_id = :salon_id,
                name = :name,
                description = :description,
                operation_time = :operation_time,
                deadline_time = :deadline_time,
                amount = :amount,
                is_coupon = :is_coupon,
                conditions = :conditions
            WHERE 
                id = :menu_id
        ";
        $stmt = $this->pdo->prepare($sql);
        $this->pdo->beginTransaction();
        try {
            $stmt->bindValue(':salon_id', $menu['salon_id'], PDO::PARAM_INT);
            $stmt->bindValue(':name', $menu['name'], PDO::PARAM_STR);
            $stmt->bindValue(':description', $menu['description'], PDO::PARAM_STR);
            $stmt->bindValue(':operation_time', $menu['operation_time'], PDO::PARAM_INT);
            $stmt->bindValue(':deadline_time', $menu['deadline_time'], PDO::PARAM_STR);
            $stmt->bindValue(':amount', $menu['amount'], PDO::PARAM_STR);
            $stmt->bindValue(':is_coupon', $menu['is_coupon'], PDO::PARAM_INT);
            $stmt->bindValue(':conditions', $menu['conditions'], PDO::PARAM_STR);
            $stmt->bindValue(':menu_id', $menu['menu_id'], PDO::PARAM_INT);
            $result = $stmt->execute();
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
        return $result;
    }

    public function delete(int $menuId): bool
    {
        $sql = "
            UPDATE 
                menus 
            SET 
                deleted_at = now() 
            WHERE 
                id = :id
            ";
        $stmt = $this->pdo->prepare($sql);
        $this->pdo->beginTransaction();
        try {
            $stmt->bindValue(':id', $menuId, PDO::PARAM_STR);
            $result = $stmt->execute();
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
        return $result;
    }
}