<?php

namespace App\Http\Controllers;

use App\Models\ThreadModel;

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
}

