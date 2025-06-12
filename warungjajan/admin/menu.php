<?php
require 'partials/header.php';
require 'partials/sidebar.php';

// Ambil data kategori untuk dropdown
$categories = $conn->query("SELECT * FROM menu_category ORDER BY name ASC");

// Aksi Tambah atau Edit (Logika PHP ini tidak banyak berubah)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $name = $_POST['name'];
    $harga = $_POST['harga'];
    $category_id = $_POST['category_id'];
    $gambar_lama = $_POST['gambar_lama'] ?? null;
    $nama_gambar = $gambar_lama; 

    // Logika upload gambar baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../uploads/menu/";
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $file_name = basename($_FILES["gambar"]["name"]);
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_type, $allowed_types)) {
            $nama_gambar_unik = uniqid() . '-' . preg_replace("/[^a-zA-Z0-9-_\.]/", "", $file_name);
            $target_file = $target_dir . $nama_gambar_unik;

            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $nama_gambar = $nama_gambar_unik;
                if ($gambar_lama && file_exists($target_dir . $gambar_lama)) {
                    unlink($target_dir . $gambar_lama);
                }
            }
        }
    }

    if ($_POST['action'] == 'add') {
        $stmt = $conn->prepare("INSERT INTO menu (name, harga, gambar, category_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdsi", $name, $harga, $nama_gambar, $category_id);
        $stmt->execute();
    } elseif ($_POST['action'] == 'edit' && isset($_POST['id'])) {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE menu SET name = ?, harga = ?, gambar = ?, category_id = ? WHERE id = ?");
        $stmt->bind_param("sdsii", $name, $harga, $nama_gambar, $category_id, $id);
        $stmt->execute();
    }
    header("Location: menu.php");
    exit();
}

// Aksi Hapus
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt_get = $conn->prepare("SELECT gambar FROM menu WHERE id = ?");
    $stmt_get->bind_param("i", $id);
    $stmt_get->execute();
    $result_get = $stmt_get->get_result();
    if($row = $result_get->fetch_assoc()){
        $gambar_dihapus = $row['gambar'];
        if ($gambar_dihapus && file_exists("../uploads/menu/" . $gambar_dihapus)) {
            unlink("../uploads/menu/" . $gambar_dihapus);
        }
    }
    $stmt_del = $conn->prepare("DELETE FROM menu WHERE id = ?");
    $stmt_del->bind_param("i", $id);
    $stmt_del->execute();
    header("Location: menu.php");
    exit();
}
?>

<h3>Kelola Menu Jajanan</h3>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Daftar Menu</span>
        <button type="button" class="btn btn-primary btn-sm" id="btnTambahMenu">
            <i class="bi bi-plus-lg"></i> Tambah Menu
        </button>
    </div>
    <div class="card-body">
        <table class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT menu.*, menu_category.name AS category_name FROM menu JOIN menu_category ON menu.category_id = menu_category.id ORDER BY menu.id DESC";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td>
                        <?php if($row['gambar'] && file_exists('../uploads/menu/'.$row['gambar'])): ?>
                            <img src="../uploads/menu/<?php echo htmlspecialchars($row['gambar']); ?>" alt="Gambar Menu" width="75" style="border-radius: 5px;">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/100x75.png?text=N/A" alt="Tidak ada gambar" width="75" style="border-radius: 5px;">
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-warning btn-edit" 
                                data-id="<?php echo $row['id']; ?>"
                                data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                data-harga="<?php echo $row['harga']; ?>"
                                data-category_id="<?php echo $row['category_id']; ?>"
                                data-gambar_lama="<?php echo $row['gambar']; ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <a href="menu.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus menu ini? Ini juga akan menghapus file gambarnya.')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="menu.php" method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="menuModalLabel">Tambah Menu Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="menu_id">
            <input type="hidden" name="action" id="menu_action" value="add">
            <input type="hidden" name="gambar_lama" id="gambar_lama">

            <div class="mb-3">
                <label for="name" class="form-label">Nama Menu</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar Menu</label>
                <input type="file" id="gambar" name="gambar" class="form-control">
                <small id="gambarHelp" class="form-text text-muted">Kosongkan jika tidak ingin mengubah gambar.</small>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="harga" class="form-label">Harga</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" id="harga" name="harga" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select id="category_id" name="category_id" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php 
                        mysqli_data_seek($categories, 0); // Reset pointer
                        while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary" id="btnSimpan">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php // Memuat jQuery dari CDN
      // Jika Anda sudah memuat jQuery di footer, Anda tidak perlu baris ini.
      // Namun untuk memastikan, kita bisa tambahkan di sini.
?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function() {
    var menuModal = new bootstrap.Modal(document.getElementById('menuModal'));

    // 1. Saat tombol "Tambah Menu" diklik
    $('#btnTambahMenu').click(function() {
        // Reset form
        $('#menuModal form')[0].reset();
        
        // Atur judul dan tombol untuk mode "Tambah"
        $('#menuModalLabel').text('Tambah Menu Baru');
        $('#btnSimpan').text('Simpan');
        $('#menu_action').val('add');
        $('#menu_id').val('');
        $('#gambar_lama').val('');
        $('#gambarHelp').hide();

        menuModal.show();
    });

    // 2. Saat tombol "Edit" di salah satu baris tabel diklik
    $('.btn-edit').click(function() {
        // Ambil data dari atribut data-* tombol yang diklik
        var id = $(this).data('id');
        var name = $(this).data('name');
        var harga = $(this).data('harga');
        var category_id = $(this).data('category_id');
        var gambar_lama = $(this).data('gambar_lama');
        
        // Isi form di dalam modal dengan data tersebut
        $('#menu_id').val(id);
        $('#name').val(name);
        $('#harga').val(harga);
        $('#category_id').val(category_id);
        $('#gambar_lama').val(gambar_lama);
        
        // Atur judul dan tombol untuk mode "Edit"
        $('#menuModalLabel').text('Edit Menu');
        $('#btnSimpan').text('Update');
        $('#menu_action').val('edit');
        $('#gambarHelp').show();

        menuModal.show();
    });
});
</script>

<?php
require 'partials/footer.php';
?>