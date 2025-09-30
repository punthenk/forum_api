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
            die("There was no data");
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
