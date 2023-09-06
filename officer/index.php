<?php
session_start();
include('../classes/Database.php');

if (!isset($_SESSION['id_petugas']) || $_SESSION['level'] !== 'petugas') {
  header('Location: ../public/login.php');
  exit();
}
?>

<?php include('../includes/header.php'); ?>

<div class="container mt-5">
  <h2>Selamat datang, <?php echo $_SESSION['nama_petugas']; ?></h2>
  <p>Anda dapat menggunakan menu di bawah ini untuk mengakses fitur-fitur.</p>
  <ul>
    <li><a href="verify_complaints.php">Verifikasi Pengaduan</a></li>
    <li><a href="responded_complaints.php">Pengaduan Ditanggapi</a></li>
  </ul>
</div>

<?php include('../includes/footer.php'); ?>