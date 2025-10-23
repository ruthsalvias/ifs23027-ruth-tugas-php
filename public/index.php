<?php
session_start();

if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 'index';
}

include('../controllers/TodoController.php');

$todoController = new TodoController();

switch ($page) {
    case 'index':
        $todoController->index();
        break;
    case 'create':
        $todoController->create();
        break;
    case 'update':
        $todoController->update();
        break;
    case 'delete':
        $todoController->delete();
        break;
    case 'detail':
        $todoController->detail();
        break;
    case 'update-sort':
        $todoController->updateSort();
        break;
    default:
        $todoController->index();
        break;
}