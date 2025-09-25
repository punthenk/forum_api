<?php

namespace App\Http\Controllers;

use App\Models\TopicModel;

class TopicController {

    public function index():bool|array {
        return TopicModel::getAll();
    }

    public function show(int $id):bool|array {
        return TopicModel::find($id);
    }
    
    public function create(array $data):bool|array {
        return TopicModel::create($data);
    }

    public function update(array $data):bool|array {
        return TopicModel::update($data);
    }
}

