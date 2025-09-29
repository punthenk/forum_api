<?php

namespace App\Http\Controllers;

use App\Models\ReplyModel;

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
}
