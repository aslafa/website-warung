<?php
// Langkah 1: Jalankan semua logika PHP SEBELUM HTML apapun.
require_once 'config/database.php';

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

// Cek jika ada form yang disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    // Query diubah untuk mencocokkan username DAN role sekaligus
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email); // Mengikat 2 parameter: username dan role
    $stmt->execute();
    $result = $stmt->get_result();

    // Cek apakah user dengan role tersebut ditemukan
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // JIKA BERHASIL:
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Alihkan berdasarkan peran
            if ($user['role'] == 'admin') {
                header("Location: admin/index.php");
            } else { // Jika rolenya 'user'
                header("Location: index.php");
            }
            exit();
        }
    }

    // Jika user tidak ditemukan, atau password salah, atau role tidak cocok
    $error = "Kombinasi Role, Username, dan Password salah!";
}

?>
<?php
$title = "Login Berkah Lestari";

$css = "<link href='./dist/css/login.css' rel='stylesheet'>";

require_once('./templates/header.php')

?>

<div class="container-fluid d-flex flex-column flex-lg-row min-vh-100 p-0">

    <div class="left-panel">
        <div class="logo-container">
            <img src="./dist/img/logo.png" alt="Berkah Lestari Logo">
        </div>
        <div class="tagline">
            <p>Belanja di</p>
            <h1>Berkah Lestari</h1>
            <p>Harga Murah Kualitas Mewah</p>
        </div>
    </div>
    <div class="right-panel">
        <div class="login-card">
            <div class="login-header">
                <h2>LOG IN</h2>
            </div>
            <form method="POST">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="login-btn">LOG IN</button>
            </form>

            <div class="register-link">
                Pengguna Baru? <a href="register.php">Daftar</a>
            </div>
        </div>
    </div>
</div>