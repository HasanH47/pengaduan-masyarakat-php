<?php
session_start();
include('../includes/header.php');
?>

<div class="container mt-5">
  <h1>Selamat datang di Aplikasi Pengaduan Masyarakat</h1>
  <p>Silakan login atau register untuk mengakses aplikasi.</p>
  <?php
  if (isset($_SESSION['user']) || isset($_SESSION['level'])) {
    if (isset($_SESSION['user'])) {
      echo '<a href="dashboard.php" class="btn btn-primary">Dashboard</a>';
    } elseif (isset($_SESSION['level']) && ($_SESSION['level'] === 'admin' || $_SESSION['level'] === 'petugas')) {
      if ($_SESSION['level'] === 'admin') {
        echo '<a href="../admin/index.php" class="btn btn-primary">Dashboard Admin</a>';
      } elseif ($_SESSION['level'] === 'petugas') {
        echo '<a href="../officer/index.php" class="btn btn-primary">Dashboard Petugas</a>';
      }
    }
  } else {
    echo '<a href="login.php" class="btn btn-primary">Login</a>';
    echo '<a href="register.php" class="btn btn-secondary">Register</a>';
  }
  ?>
</div>

<?php
include('../includes/footer.php');
?>