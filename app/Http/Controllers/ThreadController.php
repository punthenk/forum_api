<?php

namespace App\Http\Controllers;

use App\Models\ThreadModel;

class ThreadController {

    // index is the function that gets all the data
    public function index():bool|array {
        return ThreadModel->getAll();
    }
    
}

