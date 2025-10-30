<?php

namespace App\Http\Controllers;

use App\Models\ThreadModel;
use Exception;

class ThreadController {

    public function index():bool|array {
        return ThreadModel::getAll();
    }

    public function find(int $id):bool|array {
        return ThreadModel::find($id);
    }
    
    public function create(array $data):bool|array {
        return ThreadModel::create($data);
    }

    public function update(array $data):bool|array {
        return ThreadModel::update($data);
    }

    public function delete(int $id, int $userId):bool|array {
        $recourse = ThreadModel::find($id);

        if ($recourse[0]->user_id === $userId){
            return ThreadModel::delete($id);
        } else {
            throw new Exception('This user does not have permission to delete this recource');
        }
        return false;
    }
}

