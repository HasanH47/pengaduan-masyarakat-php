<?php
session_start();
include('../classes/Database.php');

if (!isset($_SESSION['nik'])) {
  header('Location: login.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_pengaduan = $_POST['id_pengaduan'];
  $foto = $_POST['foto'];

  $db = new Database();
  $conn = $db->getConnection();

  // Hapus foto jika ada
  if (!empty($foto)) {
    $upload_dir = '../assets/uploads/';
    unlink($upload_dir . $foto);
  }

  // Hapus pengaduan
  $queryDelete = "DELETE FROM pengaduan WHERE id_pengaduan = $id_pengaduan";
  $queryDeleteTanggapan = "DELETE FROM tanggapan WHERE id_pengaduan = $id_pengaduan";

  if ($conn->query($queryDelete) && $conn->query($queryDeleteTanggapan)) {
    header('Location: dashboard.php');
    exit();
  } else {
    $error_message = "Gagal menghapus pengaduan. Silakan coba lagi.";
  }
} elseif (isset($_GET['id'])) {
  $id_pengaduan = $_GET['id'];

  $db = new Database();
  $conn = $db->getConnection();

  $query = "SELECT * FROM pengaduan WHERE id_pengaduan = $id_pengaduan";
  $result = $conn->query($query);
  $pengaduan = $result->fetch_assoc();
} else {
  header('Location: dashboard.php');
  exit();
}
?>