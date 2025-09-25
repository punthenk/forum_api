<?php

namespace App\Models;

use App\Database\Database;

class ThreadModel {

    public static function getAll():bool|array {
        $query = "
            SELECT *
            FROM threads
        ";
        Database::query($query);
        // This returns the data the query get from the DB
        return Database::getAll();
    }

    public static function find(int $id):bool|array {
        $query = "
            SELECT *
            FROM threads
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
            INSERT INTO threads
            (user_id, title, description)
            VALUES (:user_id, :title, :description)
        ";
        Database::query($query, [
            "user_id" => $data['user_id'],
            "title" => $data['title'],
            "description" => $data['description'],
        ]);
        $lastID = Database::lastInsertId();

        return ['message' => 'Thread created', 'id' => $lastID] ?? [];
    }

    public static function update($data):bool|array {
        if ($data['id'] === null || empty($data['id'])) {
            // Make an error respnose here 400
            die("There was no id given");
        }


        $query = "
            UPDATE threads
            SET user_id = :user_id, title = :title, description = :description,
            WHERE id = :id
        ";
        Database::query($query, [
            "user_id" => $data['user_id'],
            "title" => $data['title'],
            "description" => $data['description'],
            ":id" => $data['id'],
        ]);
        $lastID = Database::lastInsertId();

        return ['message' => 'Thread created', 'id' => $lastID] ?? [];
    }
}
