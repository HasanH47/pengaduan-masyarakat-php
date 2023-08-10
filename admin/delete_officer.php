<?php
session_start();
include('../classes/Database.php');

// Verifikasi apakah user telah login sebagai admin
if (!isset($_SESSION['id_petugas']) || $_SESSION['level'] !== 'admin') {
  header('Location: ../public/login.php');
  exit();
}

$db = new Database();
$conn = $db->getConnection();

// Menghapus data petugas berdasarkan id_petugas yang diberikan di URL
if (isset($_GET['id'])) {
  $id_petugas = $_GET['id'];

  // Query untuk menghapus data petugas dari database
  $queryDelete = "DELETE FROM petugas WHERE id_petugas = $id_petugas";

  if ($conn->query($queryDelete)) {
    header('Location: manage_officers.php'); // Redirect kembali ke halaman kelola petugas setelah berhasil menghapus
    exit();
  } else {
    $error_message = "Gagal menghapus petugas. Silakan coba lagi.";
  }
} else {
  header('Location: manage_officers.php'); // Redirect jika tidak ada id_petugas di URL
  exit();
}
?>