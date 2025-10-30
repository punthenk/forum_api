<?php

namespace App\Http\Controllers;

use App\Models\ReplyModel;
use Exception;

class ReplyController {

    public function index():bool|array {
        return ReplyModel::getAll();
    }

    public function find(int $id):bool|array {
        return ReplyModel::find($id);
    }
    
    public function create(array $data):bool|array {
        return ReplyModel::create($data);
    }

    public function update(array $data):bool|array {
        return ReplyModel::update($data);
    }

    public function delete(int $id, int $userId):bool|array {
        $recourse = ReplyModel::find($id);

        if ($recourse[0]->user_id === $userId){
            return ReplyModel::delete($id);
        } else {
            throw new Exception('This user does not have permission to delete this recource');
        }
        return false;
    }
}
