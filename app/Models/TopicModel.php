<?php

namespace App\Models;

use App\Database\Database;
use Exception;

class TopicModel {

    public static function getAll():bool|array {
        $query = "
            SELECT *
            FROM topics
        ";
        Database::query($query);
        // This returns the data the query get from the DB
        return Database::getAll();
    }

    public static function find(int $id):bool|array {
        $query = "
            SELECT *
            FROM topics
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
            INSERT INTO topics
            (thread_id, user_id, title, body)
            VALUES (:thread_id, :user_id, :title, :body)
        ";
        Database::query($query, [
            ":thread_id" => $data['thread_id'],
            ":user_id" => $data['user_id'],
            ":title" => $data['title'],
            ":body" => $data['body'],
        ]);
        $lastID = Database::lastInsertId();

        return ['message' => 'Thread created', 'id' => $lastID] ?? [];
    }

    public static function update($data):bool|array {
        if ($data['id'] === null || empty($data['id'])) {
            // Make an error respnose here 400
            die("There was no id given");
        }

        $updateableFields = ['title', 'body'];
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
            UPDATE topics
            SET " . implode(', ', $setParts) . ", updated_at = :updated_at
            WHERE id = :id;
            SELECT ROW_COUNT()
            AS updated_rows;
        ";
        $updated = Database::query($query, $params);

        if($updated > 0) {
            return ['message' => 'Topic updated', 'id' => $data['id']];
        } else {
            throw new Exception('Topic could not be updated');
        }
    }
    
    public static function delete($id):bool|array {
        if ($id === null) {
            return false;
        }

        $query = "
            DELETE FROM
            topics
            WHERE id = :id;
            SELECT ROW_COUNT() 
            AS deleted_rows;
        ";

        $deleted = Database::query($query,
            [":id" => $id]
        );

        if ($deleted > 0) {
            return ['message' => 'Topic deleted', 'id' => $id];
        } else {
            throw new Exception('Topic could not be deleted');
        }
    }
}

