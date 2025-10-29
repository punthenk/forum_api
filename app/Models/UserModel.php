<?php

namespace App\Models;

use App\Database\Database;
use Exception;

class UserModel {

    public static function findByEmail(string $email):bool|object {
        $query = "
            SELECT *
            FROM users
            WHERE email = :email
        ";

        Database::query($query, [':email' => $email]);
        $result = Database::get();
        return $result ?? false;
    }

    public static function findByToken(string $token):bool|object {
        $now = date('Y-m-d H:i:s');
        $query = "
            SELECT *
            FROM users
            WHERE token_hash = :token
            AND token_expires_at > :now
        ";

        Database::query($query, [
            ':token' => $token,
            ':now' => $now,
        ]);
        $result = Database::get();
        return $result ?? false;
    }

    public static function create(array $data):bool|array {
        if (empty($data['email']) || empty($data['username']) || empty($data['password'])) {
            throw new Exception("Email, username and password are required");
        }

        if (self::findByEmail($data['email'])) {
            return ['error' => 'Email is already in use'];
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $query = "
            INSERT INTO users
            (email, username, password_hash)
            VALUES (:email, :username, :password)
        ";
        Database::query($query, [
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => $hashedPassword,
        ]);

        return ['message' => 'User created successfully'];
    }

    public static function clearToken(int $userId):bool {
        $query = "
            UPDATE users
            SET token_hash = NULL,
            token_created_at = NULL,
            token_expires_at = NULL
            WHERE id = :id;
            SELECT ROW_COUNT() 
            AS updated_rows;
            
        ";
        $updated = Database::query($query, [':id' => $userId]);
        return $updated > 0;
    }

    public static function setToken(string $token, string $expire_date, int $id):bool {
        $now = date('Y-m-d H:i:s');

        $query = "
            UPDATE users
            SET token_hash = :token,
            token_expires_at = :expire_date,
            token_created_at = :now
            WHERE id = :id;
            SELECT ROW_COUNT()
            AS updated_rows;
        ";

        $updated = Database::query($query, [
            ':token' => $token,
            ':expire_date' => $expire_date,
            ':now' => $now,
            ':id' => $id,
        ]);
        return $updated > 0;
    }

    public static function refreshTokenExpireDate(string $token, string $email): bool {
        $now = date('Y-m-d H:i:s');

        $query = "
            UPDATE users
            SET token_hash = :token,
            token_expires_at = :expire_date,
            token_created_at = :now
            WHERE email = :email;
            SELECT ROW_COUNT()
            AS updated_rows;
        ";

        $updated = Database::query($query, [
            ':token' => $token,
            ':expire_date' => $now,
            ':now' => $now,
            ':email' => $email,
        ]);
        return $updated > 0;

    }
}
