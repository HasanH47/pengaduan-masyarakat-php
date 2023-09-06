<?php
session_start();
include('../classes/Database.php');

// Verifikasi apakah user telah login sebagai admin
if (!isset($_SESSION['id_petugas']) || $_SESSION['level'] !== 'admin') {
  header('Location: ../public/login.php');
  exit();
}

if (isset($_GET['id'])) {
  $id_petugas = $_GET['id'];

  $db = new Database();
  $conn = $db->getConnection();

  // Query untuk menghapus data petugas dari database
  $queryDelete = "DELETE FROM petugas WHERE id_petugas = ?";

  $stmt = $conn->prepare($queryDelete);
  $stmt->bind_param('i', $id_petugas);

  if ($stmt->execute()) {
    header('Location: manage_officers.php'); // Redirect kembali ke halaman kelola petugas setelah berhasil menghapus
    exit();
  } else {
    $error_message = "Gagal menghapus petugas. Silakan coba lagi.";
  }
} else {
  header('Location: manage_officers.php'); // Redirect jika tidak ada id_petugas di URL
  exit();
}
