<?php
require_once(__DIR__ . '/../models/TodoModel.php');

class TodoController
{
    public function index()
    {
        $todoModel = new TodoModel();
        $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $todos = $todoModel->getAllTodos($filter, $search);
        
        // Untuk notifikasi
        $message = isset($_SESSION['message']) ? $_SESSION['message'] : null;
        $messageType = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : null;
        unset($_SESSION['message'], $_SESSION['message_type']);
        
        include(__DIR__ . '/../views/TodoView.php');
    }

    public function create()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title']);
            $description = trim($_POST['description'] ?? '');
            
            $todoModel = new TodoModel();
            $result = $todoModel->createTodo($title, $description);
            
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = $result['success'] ? 'success' : 'danger';
        }
        
        $filter = isset($_GET['filter']) ? '&filter=' . $_GET['filter'] : '';
        $search = isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
        header('Location: index.php?' . ltrim($filter . $search, '&'));
    }

    public function update()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $title = trim($_POST['title']);
            $description = trim($_POST['description'] ?? '');
            $is_finished = $_POST['is_finished'];
            
            $todoModel = new TodoModel();
            $result = $todoModel->updateTodo($id, $title, $description, $is_finished);
            
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = $result['success'] ? 'success' : 'danger';
        }
        
        $filter = isset($_GET['filter']) ? '&filter=' . $_GET['filter'] : '';
        $search = isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
        header('Location: index.php?' . ltrim($filter . $search, '&'));
    }

    public function delete()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $todoModel = new TodoModel();
            $result = $todoModel->deleteTodo($id);
            
            $_SESSION['message'] = $result ? 'Todo berhasil dihapus!' : 'Gagal menghapus todo!';
            $_SESSION['message_type'] = $result ? 'success' : 'danger';
        }
        
        $filter = isset($_GET['filter']) ? '&filter=' . $_GET['filter'] : '';
        $search = isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
        header('Location: index.php?' . ltrim($filter . $search, '&'));
    }

    public function detail()
    {
        if (isset($_GET['id'])) {
            $todoModel = new TodoModel();
            $todo = $todoModel->getTodoById($_GET['id']);
            
            if ($todo) {
                include(__DIR__ . '/../views/TodoDetailView.php');
            } else {
                header('Location: index.php');
            }
        } else {
            header('Location: index.php');
        }
    }

    public function updateSort()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            if (isset($data['sortOrder'])) {
                $todoModel = new TodoModel();
                $result = $todoModel->updateSortOrder($data['sortOrder']);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid data']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }
}