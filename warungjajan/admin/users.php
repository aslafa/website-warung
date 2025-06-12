<?php
require 'partials/header.php';
require 'partials/sidebar.php';

// Logika untuk Aksi (Tambah, Edit, Hapus)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    if ($_POST['action'] == 'add') {
        $password = $_POST['password'];
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt_check->bind_param("ss", $username, $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        if ($result_check->num_rows > 0) {
            $_SESSION['error_message'] = "Gagal menambahkan. Username atau Email sudah terdaftar.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $password_hash, $role);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Pengguna baru berhasil ditambahkan!";
            } else {
                $_SESSION['error_message'] = "Terjadi kesalahan saat menyimpan ke database.";
            }
        }
    } 
    elseif ($_POST['action'] == 'edit' && isset($_POST['id'])) {
        $id = $_POST['id'];
        $password = $_POST['password'];
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ?, password = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $username, $email, $role, $password_hash, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
            $stmt->bind_param("sssi", $username, $email, $role, $id);
        }
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Data pengguna berhasil diperbarui!";
        } else {
            $_SESSION['error_message'] = "Terjadi kesalahan saat memperbarui data.";
        }
    }
    header("Location: users.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: users.php");
    exit();
}
?>

<h3>Kelola Pengguna</h3>

<?php
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $_SESSION['success_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $_SESSION['error_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['error_message']);
}
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Daftar Pengguna</span>
        <button type="button" class="btn btn-primary btn-sm" id="btnTambahPengguna">
            <i class="bi bi-person-plus-fill"></i> Tambah Pengguna
        </button>
    </div>
    <div class="card-body">
        <table class="table table-striped table-bordered align-middle">
            <thead>
                <tr><th>#</th><th>Username</th><th>Email</th><th>Role</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT id, username, email, role FROM users ORDER BY id DESC");
                $no = 1;
                while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><span class="badge bg-<?php echo ($row['role'] == 'admin') ? 'success' : 'secondary'; ?>"><?php echo ucfirst($row['role']); ?></span></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-warning btn-edit" data-id="<?php echo $row['id']; ?>" data-username="<?php echo htmlspecialchars($row['username']); ?>" data-email="<?php echo htmlspecialchars($row['email']); ?>" data-role="<?php echo $row['role']; ?>"><i class="bi bi-pencil"></i></button>
                        <?php if ($row['id'] != $_SESSION['user_id']): ?>
                        <a href="users.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus pengguna ini?')"><i class="bi bi-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="users.php" method="post">
                <div class="modal-header"><h5 class="modal-title" id="userModalLabel">...</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="user_id">
                    <input type="hidden" name="action" id="user_action" value="add">
                    <div class="mb-3"><label for="username" class="form-label">Username</label><input type="text" id="username" name="username" class="form-control" required></div>
                    <div class="mb-3"><label for="email" class="form-label">Email</label><input type="email" id="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label for="password" class="form-label">Password</label><input type="password" id="password" name="password" class="form-control"><small id="passwordHelp" class="form-text text-muted">Kosongkan jika tidak ingin mengubah.</small></div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select id="role" name="role" class="form-select" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button><button type="submit" class="btn btn-primary" id="btnSimpan">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    var userModal = new bootstrap.Modal(document.getElementById('userModal'));
    $('#btnTambahPengguna').click(function() {
        $('#userModal form')[0].reset();
        $('#userModalLabel').text('Tambah Pengguna Baru');
        $('#btnSimpan').text('Simpan');
        $('#user_action').val('add');
        $('#user_id').val('');
        $('#password').prop('required', true);
        $('#passwordHelp').hide();
        userModal.show();
    });
    $('.btn-edit').click(function() {
        $('#userModal form')[0].reset();
        var id = $(this).data('id');
        var username = $(this).data('username');
        var email = $(this).data('email');
        var role = $(this).data('role');
        $('#user_id').val(id);
        $('#username').val(username);
        $('#email').val(email);
        $('#role').val(role);
        $('#userModalLabel').text('Edit Pengguna');
        $('#btnSimpan').text('Update');
        $('#user_action').val('edit');
        $('#password').prop('required', false);
        $('#passwordHelp').show();
        userModal.show();
    });
});
</script>

<?php require 'partials/footer.php'; ?>