<?php
session_start();
include('../classes/Database.php');

if (!isset($_SESSION['id_petugas']) || $_SESSION['level'] !== 'petugas') {
  header('Location: ../public/login.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_pengaduan = $_POST['id_pengaduan'];
  $new_status = $_POST['status'];

  $db = new Database();
  $conn = $db->getConnection();

  // Periksa apakah pengaduan ada dan memiliki tanggapan oleh petugas yang sesuai
  $query = "
    SELECT pengaduan.*, tanggapan.id_petugas
    FROM pengaduan
    INNER JOIN tanggapan ON pengaduan.id_pengaduan = tanggapan.id_pengaduan
    WHERE pengaduan.id_pengaduan = $id_pengaduan AND tanggapan.id_petugas = {$_SESSION['id_petugas']}
  ";

  $result = $conn->query($query);

  if ($result && $result->num_rows > 0) {
    // Update status pengaduan
    $updateQuery = "UPDATE pengaduan SET status = '$new_status' WHERE id_pengaduan = $id_pengaduan";

    if ($conn->query($updateQuery)) {
      header('Location: responded_complaints.php'); // Redirect kembali ke halaman daftar pengaduan yang ditanggapi
      exit();
    } else {
      $error_message = "Gagal mengupdate status pengaduan. Silakan coba lagi.";
    }
  } else {
    header('Location: responded_complaints.php'); // Redirect kembali ke halaman daftar pengaduan yang ditanggapi
    exit();
  }
}
