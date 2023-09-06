<?php
session_start();
if (!isset($_SESSION['id_petugas']) || $_SESSION['level'] !== 'admin') {
  header('Location: ../public/login.php');
  exit();
}
?>

<?php include('../includes/header.php'); ?>

<div class="container mt-5">
  <h2>Admin Dashboard</h2>
  <p>Selamat datang di Dasbor Admin. Berikut adalah beberapa fitur yang dapat Anda akses:</p>
  <ul>
    <li><a href="manage_officers.php">Kelola Petugas</a></li>
    <li><a href="verify_complaints.php">Verifikasi Pengaduan</a></li>
    <li><a href="responded_complaints.php">Pengaduan Ditanggapi</a></li>
    <li><a href="generate_report.php">Generate Laporan</a></li>
  </ul>
</div>

<?php include('../includes/footer.php'); ?>