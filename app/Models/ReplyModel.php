<?php

namespace App\Models;

use App\Database\Database;

class ReplyModel {

    public static function getAll():bool|array {
        $query = "
            SELECT *
            FROM replies
        ";
        Database::query($query);
        // This returns the data the query get from the DB
        return Database::getAll();
    }

    public static function find(int $id):bool|array {
        $query = "
            SELECT *
            FROM replies
            WHERE id = :id
        ";
        Database::query($query, [
            ":id" => $id,
        ]);
        // This returns the data the query get from the DB with the specific ID
        return Database::getAll();
    }

    public static function create($data):bool|array {
        if ($data === null || empty($data)) {
            return false;
        }

        $query = "
            INSERT INTO replies
            (topic_id, user_id, body)
            VALUES (:topic_id, :user_id, :body)
        ";
        Database::query($query, [
            ":topic_id" => $data['topic_id'],
            ":user_id" => $data['user_id'],
            ":body" => $data['body'],
        ]);
        $lastID = Database::lastInsertId();

        return ['message' => 'Reply created', 'id' => $lastID] ?? [];
    }

    public static function update($data):bool|array {
        if ($data['id'] === null || empty($data['id'])) {
            // Make an error respnose here 400
            die("There was no id given");
        }

        $updateableFields = ['body'];
        $setParts = [];
        $params = ['id' => $data['id'], 'updated_at' => date('Y-m-d H:i:s')];

        foreach ($updateableFields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $setParts[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }

        if (empty($setParts)) {
            // Or set an error response 400
            return false;
        }

        $query = "
            UPDATE replies
            SET " . implode(', ', $setParts) . ", updated_at = :updated_at
            WHERE id = :id
        ";
        Database::query($query, $params);

        return ['message' => 'Thread updated', 'id' => $data['id']] ?? [];
    }

    public static function delete($id):bool|array {
        if ($id === null) {
            return false;
        }

        $query = "
            DELETE FROM
            replies
            WHERE id = :id
        ";

        Database::query($query,
            [":id" => $id]
        );
        return ['message' => 'Thread deleted', 'id' => $id] ?? null;
    }
}
