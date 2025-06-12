<?php
require 'partials/header.php';
require 'partials/sidebar.php';

// Aksi Tambah atau Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];

    if ($_POST['action'] == 'add') {
        $stmt = $conn->prepare("INSERT INTO menu_category (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
        $stmt->execute();
    } elseif ($_POST['action'] == 'edit' && isset($_POST['id'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE menu_category SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $description, $id);
        $stmt->execute();
    }
    header("Location: kategori.php");
    exit();
}

// Aksi Hapus
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM menu_category WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: kategori.php");
    exit();
}

// Ambil data untuk form edit
$edit_category = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM menu_category WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_category = $result->fetch_assoc();
}
?>

<h3>Kelola Kategori Menu</h3>
<div class="card mb-4">
    <div class="card-header"><?php echo $edit_category ? 'Edit' : 'Tambah'; ?> Kategori</div>
    <div class="card-body">
        <form action="kategori.php" method="post">
            <input type="hidden" name="action" value="<?php echo $edit_category ? 'edit' : 'add'; ?>">
            <?php if ($edit_category): ?>
                <input type="hidden" name="id" value="<?php echo $edit_category['id']; ?>">
            <?php endif; ?>
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Nama Kategori" required value="<?php echo $edit_category['name'] ?? ''; ?>">
                </div>
                <div class="col-md-6">
                    <input type="text" name="description" class="form-control" placeholder="Deskripsi Singkat" value="<?php echo $edit_category['description'] ?? ''; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><?php echo $edit_category ? 'Update' : 'Tambah'; ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Nama Kategori</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result = $conn->query("SELECT * FROM menu_category ORDER BY name");
        $no = 1;
        while ($row = $result->fetch_assoc()):
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td>
                <a href="kategori.php?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                <a href="kategori.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus kategori ini?')"><i class="bi bi-trash"></i></a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php
require 'partials/footer.php';
?>