<?php
require_once(__DIR__ . '/../config.php');

class TodoModel
{
    private $conn;

    public function __construct()
    {
        // Inisialisasi koneksi database PostgreSQL
        $this->conn = pg_connect('host=' . DB_HOST . ' port=' . DB_PORT . ' dbname=' . DB_NAME . ' user=' . DB_USER . ' password=' . DB_PASSWORD);
        if (!$this->conn) {
            die('Koneksi database gagal');
        }
    }

    public function getAllTodos($filter = 'all', $search = '')
    {
        $query = 'SELECT * FROM todo WHERE 1=1';
        $params = [];
        $paramCount = 1;

        // Filter berdasarkan status
        if ($filter === 'finished') {
            $query .= ' AND is_finished = TRUE';
        } elseif ($filter === 'unfinished') {
            $query .= ' AND is_finished = FALSE';
        }

        // Pencarian
        if (!empty($search)) {
            $query .= ' AND (title ILIKE $' . $paramCount . ' OR description ILIKE $' . $paramCount . ')';
            $params[] = '%' . $search . '%';
            $paramCount++;
        }

        $query .= ' ORDER BY sort_order ASC, created_at DESC';

        $result = pg_query_params($this->conn, $query, $params);
        $todos = [];
        if ($result && pg_num_rows($result) > 0) {
            while ($row = pg_fetch_assoc($result)) {
                $todos[] = $row;
            }
        }
        return $todos;
    }

    public function getTodoById($id)
    {
        $query = 'SELECT * FROM todo WHERE id = $1';
        $result = pg_query_params($this->conn, $query, [$id]);
        if ($result && pg_num_rows($result) > 0) {
            return pg_fetch_assoc($result);
        }
        return null;
    }

    public function createTodo($title, $description = '')
    {
        // Cek apakah title sudah ada
        if ($this->isTitleExists($title)) {
            return ['success' => false, 'message' => 'Judul todo sudah ada!'];
        }

        // Dapatkan sort_order tertinggi
        $maxOrderQuery = 'SELECT COALESCE(MAX(sort_order), -1) as max_order FROM todo';
        $maxOrderResult = pg_query($this->conn, $maxOrderQuery);
        $maxOrder = pg_fetch_assoc($maxOrderResult)['max_order'];

        $query = 'INSERT INTO todo (title, description, sort_order) VALUES ($1, $2, $3)';
        $result = pg_query_params($this->conn, $query, [$title, $description, $maxOrder + 1]);
        
        if ($result !== false) {
            return ['success' => true, 'message' => 'Todo berhasil ditambahkan!'];
        }
        return ['success' => false, 'message' => 'Gagal menambahkan todo!'];
    }

    public function updateTodo($id, $title, $description, $is_finished)
    {
        // Cek apakah title sudah ada (kecuali untuk todo yang sedang diedit)
        if ($this->isTitleExists($title, $id)) {
            return ['success' => false, 'message' => 'Judul todo sudah ada!'];
        }

        $query = 'UPDATE todo SET title=$1, description=$2, is_finished=$3 WHERE id=$4';
        $result = pg_query_params($this->conn, $query, [$title, $description, $is_finished, $id]);
        
        if ($result !== false) {
            return ['success' => true, 'message' => 'Todo berhasil diupdate!'];
        }
        return ['success' => false, 'message' => 'Gagal mengupdate todo!'];
    }

    public function deleteTodo($id)
    {
        $query = 'DELETE FROM todo WHERE id=$1';
        $result = pg_query_params($this->conn, $query, [$id]);
        return $result !== false;
    }

    public function isTitleExists($title, $excludeId = null)
    {
        if ($excludeId) {
            $query = 'SELECT COUNT(*) as count FROM todo WHERE title = $1 AND id != $2';
            $result = pg_query_params($this->conn, $query, [$title, $excludeId]);
        } else {
            $query = 'SELECT COUNT(*) as count FROM todo WHERE title = $1';
            $result = pg_query_params($this->conn, $query, [$title]);
        }
        
        $row = pg_fetch_assoc($result);
        return $row['count'] > 0;
    }

    public function updateSortOrder($sortData)
    {
        pg_query($this->conn, 'BEGIN');
        
        try {
            foreach ($sortData as $index => $id) {
                $query = 'UPDATE todo SET sort_order = $1 WHERE id = $2';
                $result = pg_query_params($this->conn, $query, [$index, $id]);
                if (!$result) {
                    throw new Exception('Failed to update sort order');
                }
            }
            pg_query($this->conn, 'COMMIT');
            return ['success' => true, 'message' => 'Sort order updated!'];
        } catch (Exception $e) {
            pg_query($this->conn, 'ROLLBACK');
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}