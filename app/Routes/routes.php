<?php

return [
    // Get data
    'GET' => [
        '/' => [App\Http\Controllers\ThreadController::class, 'index'],
        'threads' => [App\Http\Controllers\ThreadController::class, 'index'],
        'thread' => [App\Http\Controllers\ThreadController::class, 'find'],
        'topics' => [App\Http\Controllers\TopicController::class, 'index'],
        'topic' => [App\Http\Controllers\TopicController::class, 'find'],
        'replies' => [App\Http\Controllers\ReplyController::class, 'index'],
        'reply' => [App\Http\Controllers\ReplyController::class, 'find'],
    ],
    // Create
    'POST' => [
        'thread' => [App\Http\Controllers\ThreadController::class, 'create'],
        'topic' => [App\Http\Controllers\TopicController::class, 'create'],
        'reply' => [App\Http\Controllers\ReplyController::class, 'create'],
        'register' => [App\Http\Controllers\AuthController::class, 'register'],
        'login' => [App\Http\Controllers\AuthController::class, 'login'],
    ],
    // Update
    'PATCH' => [
        'thread' => [App\Http\Controllers\ThreadController::class, 'update'],
        'topic' => [App\Http\Controllers\TopicController::class, 'update'],
        'reply'  => [App\Http\Controllers\ReplyController::class, 'update'],
    ],
    'DELETE' => [
        'thread' => [App\Http\Controllers\ThreadController::class, 'delete'],
        'topic' => [App\Http\Controllers\TopicController::class, 'delete'],
        'reply'  => [App\Http\Controllers\ReplyController::class, 'delete'],
    ],

];
