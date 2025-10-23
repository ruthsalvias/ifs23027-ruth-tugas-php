<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List - Aplikasi Manajemen Tugas</title>
    <link href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #8b5cf6;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --dark-color: #1f2937;
            --light-color: #f9fafb;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .main-card {
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            background: white;
        }
        
        .header-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem;
            border-radius: 20px 20px 0 0;
        }
        
        .search-filter-section {
            background: var(--light-color);
            padding: 1.5rem;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .filter-btn {
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .filter-btn.active {
            background: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color);
        }
        
        .todo-item {
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
            cursor: move;
            background: white;
            margin-bottom: 0.75rem;
            border-radius: 10px;
            padding: 1rem;
        }
        
        .todo-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .todo-item.dragging {
            opacity: 0.5;
        }
        
        .todo-item.finished {
            border-left-color: var(--success-color);
            background: #f0fdf4;
        }
        
        .drag-handle {
            cursor: move;
            color: #9ca3af;
            font-size: 1.2rem;
        }
        
        .drag-handle:hover {
            color: var(--primary-color);
        }
        
        .badge-custom {
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-weight: 600;
        }
        
        .btn-action {
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .empty-state {
            padding: 3rem;
            text-align: center;
            color: #9ca3af;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .stats-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            display: inline-block;
            margin: 0 0.5rem;
        }
    </style>
</head>
<body>
<div class="container-fluid p-3 p-md-5">
    <div class="card main-card">
        <!-- Header -->
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="mb-2"><i class="fas fa-clipboard-check"></i> Todo List</h1>
                    <p class="mb-0">Kelola tugas Anda dengan mudah dan efisien</p>
                </div>
                <button class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#addTodo">
                    <i class="fas fa-plus-circle"></i> Tambah Todo
                </button>
            </div>
            <div class="mt-3">
                <span class="stats-badge">
                    <i class="fas fa-tasks"></i> Total: <?= count($todos) ?>
                </span>
                <span class="stats-badge">
                    <i class="fas fa-check-circle"></i> Selesai: <?= count(array_filter($todos, fn($t) => $t['is_finished'] == 't')) ?>
                </span>
                <span class="stats-badge">
                    <i class="fas fa-hourglass-half"></i> Belum: <?= count(array_filter($todos, fn($t) => $t['is_finished'] == 'f')) ?>
                </span>
            </div>
        </div>

        <!-- Alert Notifikasi -->
        <?php if (isset($message)): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show m-3" role="alert">
            <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Search & Filter Section -->
        <div class="search-filter-section">
            <form method="GET" action="index.php" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari todo berdasarkan judul atau deskripsi..." 
                               value="<?= htmlspecialchars($search ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="filter" id="filterAll" 
                               value="all" <?= (!isset($filter) || $filter === 'all') ? 'checked' : '' ?> onchange="this.form.submit()">
                        <label class="btn btn-outline-primary filter-btn <?= (!isset($filter) || $filter === 'all') ? 'active' : '' ?>" for="filterAll">
                            <i class="fas fa-list"></i> Semua
                        </label>

                        <input type="radio" class="btn-check" name="filter" id="filterUnfinished" 
                               value="unfinished" <?= (isset($filter) && $filter === 'unfinished') ? 'checked' : '' ?> onchange="this.form.submit()">
                        <label class="btn btn-outline-warning filter-btn <?= (isset($filter) && $filter === 'unfinished') ? 'active' : '' ?>" for="filterUnfinished">
                            <i class="fas fa-hourglass-half"></i> Belum Selesai
                        </label>

                        <input type="radio" class="btn-check" name="filter" id="filterFinished" 
                               value="finished" <?= (isset($filter) && $filter === 'finished') ? 'checked' : '' ?> onchange="this.form.submit()">
                        <label class="btn btn-outline-success filter-btn <?= (isset($filter) && $filter === 'finished') ? 'active' : '' ?>" for="filterFinished">
                            <i class="fas fa-check-circle"></i> Selesai
                        </label>
                    </div>
                </div>
            </form>
        </div>

        <!-- Todo List -->
        <div class="card-body p-4">
            <?php if (!empty($todos)): ?>
                <div id="todoList">
                    <?php foreach ($todos as $todo): ?>
                    <div class="todo-item <?= $todo['is_finished'] == 't' ? 'finished' : '' ?>" data-id="<?= $todo['id'] ?>">
                        <div class="d-flex align-items-center">
                            <div class="drag-handle me-3">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">
                                    <?php if ($todo['is_finished'] == 't'): ?>
                                        <del><?= htmlspecialchars($todo['title']) ?></del>
                                    <?php else: ?>
                                        <?= htmlspecialchars($todo['title']) ?>
                                    <?php endif; ?>
                                </h5>
                                <p class="mb-2 text-muted small">
                                    <?= htmlspecialchars(substr($todo['description'] ?? '', 0, 100)) ?>
                                    <?= strlen($todo['description'] ?? '') > 100 ? '...' : '' ?>
                                </p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> <?= date('d M Y, H:i', strtotime($todo['created_at'])) ?>
                                </small>
                            </div>
                            <div class="ms-3">
                                <span class="badge-custom <?= $todo['is_finished'] == 't' ? 'bg-success' : 'bg-warning' ?> mb-2 d-block">
                                    <?= $todo['is_finished'] == 't' ? '<i class="fas fa-check"></i> Selesai' : '<i class="fas fa-clock"></i> Belum Selesai' ?>
                                </span>
                                <div class="btn-group-vertical btn-group-sm">
                                    <a href="?page=detail&id=<?= $todo['id'] ?>" class="btn btn-info btn-action text-white">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <button class="btn btn-warning btn-action" 
                                        onclick="showModalEditTodo(<?= $todo['id'] ?>, '<?= htmlspecialchars(addslashes($todo['title'])) ?>', '<?= htmlspecialchars(addslashes($todo['description'] ?? '')) ?>', '<?= $todo['is_finished'] ?>')">
                                        <i class="fas fa-edit"></i> Ubah
                                    </button>
                                    <button class="btn btn-danger btn-action" 
                                        onclick="showModalDeleteTodo(<?= $todo['id'] ?>, '<?= htmlspecialchars(addslashes($todo['title'])) ?>')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h4>Belum ada todo</h4>
                    <p>Mulai tambahkan todo baru untuk mengelola tugas Anda!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL ADD TODO -->
<div class="modal fade" id="addTodo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Tambah Todo Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="?page=create<?= isset($filter) ? '&filter=' . $filter : '' ?><?= isset($search) ? '&search=' . urlencode($search) : '' ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inputTitle" class="form-label">
                            <i class="fas fa-heading"></i> Judul <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title" class="form-control" id="inputTitle"
                            placeholder="Contoh: Belajar PHP" required maxlength="250">
                    </div>
                    <div class="mb-3">
                        <label for="inputDescription" class="form-label">
                            <i class="fas fa-align-left"></i> Deskripsi
                        </label>
                        <textarea name="description" class="form-control" id="inputDescription" rows="4"
                            placeholder="Tambahkan detail tentang todo ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT TODO -->
<div class="modal fade" id="editTodo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Ubah Todo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="?page=update<?= isset($filter) ? '&filter=' . $filter : '' ?><?= isset($search) ? '&search=' . urlencode($search) : '' ?>" method="POST">
                <input name="id" type="hidden" id="inputEditTodoId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inputEditTitle" class="form-label">
                            <i class="fas fa-heading"></i> Judul <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title" class="form-control" id="inputEditTitle" required maxlength="250">
                    </div>
                    <div class="mb-3">
                        <label for="inputEditDescription" class="form-label">
                            <i class="fas fa-align-left"></i> Deskripsi
                        </label>
                        <textarea name="description" class="form-control" id="inputEditDescription" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="selectEditStatus" class="form-label">
                            <i class="fas fa-tasks"></i> Status
                        </label>
                        <select class="form-select" name="is_finished" id="selectEditStatus">
                            <option value="0">Belum Selesai</option>
                            <option value="1">Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DELETE TODO -->
<div class="modal fade" id="deleteTodo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Hapus Todo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                <h5>Apakah Anda yakin?</h5>
                <p class="mb-0">Anda akan menghapus todo: <strong class="text-danger" id="deleteTodoTitle"></strong></p>
                <p class="text-muted small">Tindakan ini tidak dapat dibatalkan!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <a id="btnDeleteTodo" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Ya, Hapus
                </a>
            </div>
        </div>
    </div>
</div>

<script src="/assets/vendor/bootstrap-5.3.8-dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
// Modal Edit Todo
function showModalEditTodo(todoId, title, description, isFinished) {
    document.getElementById("inputEditTodoId").value = todoId;
    document.getElementById("inputEditTitle").value = title;
    document.getElementById("inputEditDescription").value = description;
    document.getElementById("selectEditStatus").value = isFinished === 't' || isFinished === '1' ? '1' : '0';
    var myModal = new bootstrap.Modal(document.getElementById("editTodo"));
    myModal.show();
}

// Modal Delete Todo
function showModalDeleteTodo(todoId, title) {
    document.getElementById("deleteTodoTitle").innerText = title;
    const currentFilter = new URLSearchParams(window.location.search).get('filter') || '';
    const currentSearch = new URLSearchParams(window.location.search).get('search') || '';
    let deleteUrl = `?page=delete&id=${todoId}`;
    if (currentFilter) deleteUrl += `&filter=${currentFilter}`;
    if (currentSearch) deleteUrl += `&search=${encodeURIComponent(currentSearch)}`;
    document.getElementById("btnDeleteTodo").setAttribute("href", deleteUrl);
    var myModal = new bootstrap.Modal(document.getElementById("deleteTodo"));
    myModal.show();
}

// Drag and Drop Sorting
const todoList = document.getElementById('todoList');
if (todoList) {
    const sortable = new Sortable(todoList, {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'dragging',
        onEnd: function(evt) {
            // Ambil urutan baru
            const items = todoList.querySelectorAll('.todo-item');
            const sortOrder = Array.from(items).map(item => item.getAttribute('data-id'));
            
            // Kirim ke server
            fetch('?page=update-sort', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    sortOrder: sortOrder
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Tampilkan notifikasi sukses (optional)
                    console.log('Sort order updated successfully');
                } else {
                    console.error('Failed to update sort order');
                    // Reload halaman jika gagal
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                location.reload();
            });
        }
    });
}

// Auto dismiss alert
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>
</body>
</html