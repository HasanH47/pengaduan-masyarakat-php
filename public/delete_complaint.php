<?php
session_start();
include('../classes/Database.php');

if (!isset($_SESSION['nik'])) {
  header('Location: login.php');
  exit();
}

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_pengaduan = $_POST['id_pengaduan'];
  $foto = $_POST['foto'];

  // Hapus foto jika ada
  if (!empty($foto)) {
    $upload_dir = '../assets/uploads/';
    $foto_path = $upload_dir . $foto;

    if (file_exists($foto_path)) {
      unlink($foto_path);
    }
  }

  // Hapus pengaduan dan tanggapan terkait
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

  $query = "SELECT * FROM pengaduan WHERE id_pengaduan = $id_pengaduan";
  $result = $conn->query($query);

  if ($result->num_rows === 1) {
    $pengaduan = $result->fetch_assoc();
  } else {
    header('Location: dashboard.php');
    exit();
  }
} else {
  header('Location: dashboard.php');
  exit();
}
?>