<?php

require_once('./config/database.php');

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // GANTI NAMA TABEL: db_warungjajan_users -> users
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $error = "Username atau Email sudah terdaftar!";
    } else {
        // GANTI NAMA TABEL: db_warungjajan_users -> users
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->bind_param("sss", $username, $email, $password_hash);
        if ($stmt->execute()) {
            $success = "Registrasi berhasil! Silakan <a href='login.php'>login</a>.";
        } else {
            $error = "Registrasi gagal, coba lagi.";
        }
    }
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
                <h2>REGISTER</h2>
            </div>
            <form action="#" method="POST">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="login-btn">DAFTAR</button>
            </form>

            <div class="register-link">
                Sudah Punya Akun? <a href="login.php">Login</a>
            </div>
        </div>
    </div>
</div>