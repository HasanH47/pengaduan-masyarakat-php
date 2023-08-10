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

// Mengambil data petugas berdasarkan id_petugas yang diberikan di URL
if (isset($_GET['id'])) {
  $id_petugas = $_GET['id'];

  // Query untuk mengambil data petugas dari database
  $query = "SELECT * FROM petugas WHERE id_petugas = $id_petugas";
  $result = $conn->query($query);

  if (!$result || $result->num_rows === 0) {
    header('Location: manage_officers.php'); // Redirect jika id_petugas tidak ditemukan
    exit();
  }

  $row = $result->fetch_assoc();
} else {
  header('Location: manage_officers.php'); // Redirect jika tidak ada id_petugas di URL
  exit();
}

// Proses data ketika formulir disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_petugas = $_POST['nama_petugas'];
  $username = $_POST['username'];
  $level = $_POST['level'];

  // Query untuk mengupdate data petugas di database
  $queryUpdate = "UPDATE petugas SET nama_petugas = '$nama_petugas', username = '$username', level = '$level' WHERE id_petugas = $id_petugas";

  if ($conn->query($queryUpdate)) {
    header('Location: manage_officers.php'); // Redirect kembali ke halaman kelola petugas setelah berhasil mengupdate
    exit();
  } else {
    $error_message = "Gagal mengupdate petugas. Silakan coba lagi.";
  }
}
?>

<?php include('../includes/header.php'); ?>

<div class="container mt-5">
  <h2>Edit Petugas</h2>
  <?php if (isset($error_message)) { ?>
    <div class="alert alert-danger" role="alert">
      <?php echo $error_message; ?>
    </div>
  <?php } ?>
  <form method="post">
    <div class="mb-3">
      <label for="nama_petugas" class="form-label">Nama Petugas</label>
      <input type="text" class="form-control" id="nama_petugas" name="nama_petugas" value="<?php echo $row['nama_petugas']; ?>" required>
    </div>
    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input type="text" class="form-control" id="username" name="username" value="<?php echo $row['username']; ?>" required>
    </div>
    <div class="mb-3">
      <label for="level" class="form-label">Level</label>
      <select class="form-select" id="level" name="level" required>
        <option value="admin" <?php if ($row['level'] === 'admin') echo 'selected'; ?>>Admin</option>
        <option value="petugas" <?php if ($row['level'] === 'petugas') echo 'selected'; ?>>Petugas</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Update Petugas</button>
  </form>
  <a href="manage_officers.php" class="btn btn-secondary mt-3">Kembali ke Kelola Petugas</a>
</div>

<?php include('../includes/footer.php'); ?>