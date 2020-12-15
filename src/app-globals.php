<?php

namespace App;
if (!defined('FRAMEWORK')) die;
$app->registerRoute('GET /', Controller\TaskController::class, 'index');
$app->registerRoute('GET /tasks/order/{orderAsc}', Controller\TaskController::class, 'index');
$app->registerRoute('POST /tasks/edit/{taskId}', Controller\TaskController::class, 'edit');
$app->registerRoute('GET /tasks/edit/{taskId}/done', Controller\TaskController::class, 'done');
$app->registerRoute('POST /tasks/add', Controller\TaskController::class, 'add');
$app->registerRoute('GET|POST /auth', Controller\AuthController::class, 'index');
$app->registerRoute('GET /auth/logout', Controller\AuthController::class, 'logout');
$app->registerService('db', Service\Db::class);
$app->registerService('auth', Service\Auth::class, true);
