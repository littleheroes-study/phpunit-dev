<?php

namespace App\Models;

use \PDO;
use App\Models\BaseModel;

class Reservation extends BaseModel
{
    /*
    |--------------------------------------------------------------------------
    | 予約モデル
    |--------------------------------------------------------------------------
    */
    public function getAll(): array
    {
        $sql = "
            SELECT 
                reservations.id,
                reservations.customer_id,
                customers.name AS customer_name,
                reservations.menu_id,
                menus.name AS menu_name,
                menus.salon_id,
                salons.name AS salon_name,
                reservations.total_amount
            FROM 
                reservations
            INNER JOIN
                customers
            ON
                customers.id=reservations.customer_id
            INNER JOIN
                menus
            ON
                menus.id=reservations.menu_id
            INNER JOIN
                salons
            ON
                salons.id=menus.salon_id
            WHERE 
                reservations.deleted_at IS NULL
            ";
        $stmt = $this->pdo->query($sql);
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $reservations;
    }

    public function findById(int $id): array|false
    {
        $sql = "
            SELECT 
                reservations.id,
                reservations.customer_id,
                customers.name AS customer_name,
                reservations.menu_id,
                menus.name AS menu_name,
                menus.salon_id,
                salons.name AS salon_name,
                reservations.total_amount,
                reservations.stylist_id,
                reservations.is_first,
                reservations.total_amount,
                reservations.visit_at
            FROM 
                reservations
            INNER JOIN
                customers
            ON
                customers.id=reservations.customer_id
            INNER JOIN
                menus
            ON
                menus.id=reservations.menu_id
            INNER JOIN
                salons
            ON
                salons.id=menus.salon_id
            WHERE 
                reservations.id=:id
            AND 
                reservations.deleted_at IS NULL
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $salon = $stmt->fetch(PDO::FETCH_ASSOC);
        return $salon;
    }

    public function create(array $reservation): false|int
    {
        $sql = "
            INSERT INTO reservations 
                (
                    customer_id,
                    menu_id,
                    stylist_id,
                    is_first,
                    total_amount,
                    visit_start_at,
                    visit_end_at
                )
            VALUES 
                (
                    :customer_id,
                    :menu_id,
                    :stylist_id,
                    :is_first,
                    :total_amount,
                    :visit_start_at,
                    :visit_end_at
                )
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':customer_id', $reservation['customer_id'], PDO::PARAM_INT);
        $stmt->bindValue(':menu_id', $reservation['menu_id'], PDO::PARAM_INT);
        $stmt->bindValue(':stylist_id', $reservation['stylist_id'], PDO::PARAM_INT);
        $stmt->bindValue(':is_first', $reservation['is_first'], PDO::PARAM_INT);
        $stmt->bindValue(':total_amount', $reservation['total_amount'], PDO::PARAM_INT);
        $stmt->bindValue(':visit_start_at', $reservation['visit_start_at'], PDO::PARAM_STR);
        $stmt->bindValue(':visit_end_at', $reservation['visit_end_at'], PDO::PARAM_STR);
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
                reservations 
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
                reservations 
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

    public function findByVisitAt($operationStartTime, $operationEndTime, $salonId, ?int $stylistId): int
    {
        // var_dump($operationStartTime, $operationEndTime);
        // exit;
        $addSql = "count(reservations.id) < (SELECT count(stylists.id) FROM stylists WHERE stylists.salon_id = :salon_id)";
        if (!empty($stylistId)) {
            $addSql = "reservations.stylist_id = :stylist_id";
        }
        $sql = "
            SELECT 
                count(id) AS count
            FROM 
                reservations
            WHERE
                visit_start_at
            BETWEEN :operation_start_time AND :operation_end_time
            AND
                visit_end_at
            BETWEEN :operation_start_time AND :operation_end_time
            AND 
                $addSql
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':operation_start_time', $operationStartTime, PDO::PARAM_STR);
        $stmt->bindValue(':operation_end_time', $operationEndTime, PDO::PARAM_STR);
        if (!empty($stylistId)) {
            $stmt->bindValue(':stylist_id', $stylistId, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(':salon_id', $salonId, PDO::PARAM_INT);
        }
        $stmt->execute();
        $salon = $stmt->fetch(PDO::FETCH_ASSOC);
        return $salon['count'];
    }
}
