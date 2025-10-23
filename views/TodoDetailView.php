<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Todo - <?= htmlspecialchars($todo['title']) ?></title>
    <link href="/assets/vendor/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #8b5cf6;
            --success-color: #10b981;
            --danger-color: #ef4444;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .detail-card {
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            background: white;
            overflow: hidden;
        }
        
        .detail-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 3rem 2rem;
        }
        
        .status-badge {
            font-size: 1.2rem;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
        }
        
        .info-section {
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 1.5rem;
        }
        
        .info-label {
            font-weight: 600;
            color: #6b7280;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        
        .info-value {
            font-size: 1.1rem;
            color: #1f2937;
        }
        
        .btn-action {
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Back Button -->
            <div class="mb-3">
                <a href="index.php" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Todo
                </a>
            </div>

            <!-- Detail Card -->
            <div class="detail-card">
                <!-- Header -->
                <div class="detail-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="mb-3">
                                <i class="fas fa-clipboard-check"></i>
                                <?= htmlspecialchars($todo['title']) ?>
                            </h1>
                            <span class="status-badge badge <?= $todo['is_finished'] == 't' ? 'bg-success' : 'bg-warning' ?>">
                                <?= $todo['is_finished'] == 't' ? '<i class="fas fa-check-circle"></i> Selesai' : '<i class="fas fa-clock"></i> Belum Selesai' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-4">
                    <!-- Deskripsi -->
                    <div class="info-section">
                        <div class="info-label">
                            <i class="fas fa-align-left"></i> Deskripsi
                        </div>
                        <div class="info-value">
                            <?php if (!empty($todo['description'])): ?>
                                <?= nl2br(htmlspecialchars($todo['description'])) ?>
                            <?php else: ?>
                                <span class="text-muted fst-italic">Tidak ada deskripsi</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Informasi Waktu -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">
                                    <i class="fas fa-calendar-plus"></i> Tanggal Dibuat
                                </div>
                                <div class="info-value">
                                    <?= date('l, d F Y', strtotime($todo['created_at'])) ?>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> <?= date('H:i', strtotime($todo['created_at'])) ?> WIB
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">
                                    <i class="fas fa-calendar-check"></i> Terakhir Diupdate
                                </div>
                                <div class="info-value">
                                    <?= date('l, d F Y', strtotime($todo['updated_at'])) ?>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i> <?= date('H:i', strtotime($todo['updated_at'])) ?> WIB
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ID Todo -->
                    <div class="info-section">
                        <div class="info-label">
                            <i class="fas fa-hashtag"></i> ID Todo
                        </div>
                        <div class="info-value">
                            <code class="bg-light px-2 py-1 rounded">#<?= $todo['id'] ?></code>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 mt-4">
                        <button class="btn btn-warning btn-action flex-fill" 
                            onclick="showModalEditTodo(<?= $todo['id'] ?>, '<?= htmlspecialchars(addslashes($todo['title'])) ?>', '<?= htmlspecialchars(addslashes($todo['description'] ?? '')) ?>', '<?= $todo['is_finished'] ?>')">
                            <i class="fas fa-edit"></i> Ubah Todo
                        </button>
                        <button class="btn btn-danger btn-action flex-fill" 
                            onclick="showModalDeleteTodo(<?= $todo['id'] ?>, '<?= htmlspecialchars(addslashes($todo['title'])) ?>')">
                            <i class="fas fa-trash"></i> Hapus Todo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT TODO -->
<div class="modal fade" id="editTodo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; border-radius: 15px 15px 0 0;">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Ubah Todo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="?page=update" method="POST">
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
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-danger text-white" style="border-radius: 15px 15px 0 0;">
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
<script>
function showModalEditTodo(todoId, title, description, isFinished) {
    document.getElementById("inputEditTodoId").value = todoId;
    document.getElementById("inputEditTitle").value = title;
    document.getElementById("inputEditDescription").value = description;
    document.getElementById("selectEditStatus").value = isFinished === 't' || isFinished === '1' ? '1' : '0';
    var myModal = new bootstrap.Modal(document.getElementById("editTodo"));
    myModal.show();
}

function showModalDeleteTodo(todoId, title) {
    document.getElementById("deleteTodoTitle").innerText = title;
    document.getElementById("btnDeleteTodo").setAttribute("href", `?page=delete&id=${todoId}`);
    var myModal = new bootstrap.Modal(document.getElementById("deleteTodo"));
    myModal.show();
}
</script>
</body>
</html>