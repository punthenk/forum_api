<?php

namespace App\Http\Controllers;

use App\Models\TopicModel;
use Exception;

class TopicController {

    public function index():bool|array {
        return TopicModel::getAll();
    }

    public function find(int $id):bool|array {
        return TopicModel::find($id);
    }
    
    public function create(array $data):bool|array {
        return TopicModel::create($data);
    }

    public function update(array $data, int $userId):bool|array {
        $recourse = TopicModel::find($data['id']);

        if ($recourse[0]->user_id === $userId){
            return TopicModel::update($data);
        } else {
            throw new Exception('This user does not have permission to update this recource');
        }
        return false;
    }

    public function delete(int $id, int $userId):bool|array {
        $recourse = TopicModel::find($id);

        if ($recourse[0]->user_id === $userId){
            return TopicModel::delete($id);
        } else {
            throw new Exception('This user does not have permission to delete this recource');
        }
        return false;
    }
}

