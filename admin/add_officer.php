<?php
session_start();
include('../classes/Database.php');

if (!isset($_SESSION['id_petugas']) || $_SESSION['level'] !== 'admin') {
  header('Location: ../public/login.php');
  exit();
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama_petugas = $_POST['nama_petugas'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Enkripsi password menggunakan hash
  $level = $_POST['level'];
  $telp = $_POST['telp'];

  $valid = true; // Tandai bahwa data valid secara default

  // Validasi input
  if (!preg_match('/^[A-Za-z\s]+$/', $nama_petugas)) {
    $error_message .= "Nama petugas hanya boleh berisi huruf saja. ";
    $valid = false;
  }
  if (!preg_match('/^[0-9]{10,13}$/', $telp)) {
    $error_message .= "Nomor telepon hanya boleh berisi angka dan memiliki panjang 10 hingga 13 digit. ";
    $valid = false;
  }

  if ($valid) {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "INSERT INTO petugas (nama_petugas, username, password, level, telp) VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssss', $nama_petugas, $username, $hashedPassword, $level, $telp);

    if ($stmt->execute()) {
      header('Location: manage_officers.php');
      exit();
    } else {
      $error_message = "Gagal menambahkan petugas. Silakan coba lagi.";
    }
  }
}
?>

<?php include('../includes/header.php'); ?>

<div class="container mt-5">
  <h2>Tambah Petugas</h2>
  <?php if (!empty($error_message)) { ?>
    <div class="alert alert-danger" role="alert">
      <?php echo $error_message; ?>
    </div>
  <?php } ?>
  <form method="post">
    <div class="mb-3">
      <label for="nama_petugas" class="form-label">Nama Petugas</label>
      <input type="text" class="form-control" id="nama_petugas" name="nama_petugas" pattern="[A-Za-z\s]+" title="Nama petugas hanya boleh berisi huruf saja" required>
    </div>
    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="mb-3">
      <label for="telp" class="form-label">Telepon</label>
      <input type="tel" class="form-control" id="telp" name="telp" pattern="[0-9]{10,13}" title="Nomor telepon hanya boleh berisi angka dan memiliki panjang 10 hingga 13 digit" required maxlength="13">
    </div>
    <div class="mb-3">
      <label for="level" class="form-label">Level</label>
      <select class="form-select" id="level" name="level" required>
        <option value="admin">Admin</option>
        <option value="petugas">Petugas</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Tambah Petugas</button>
  </form>
  <a href="manage_officers.php" class="btn btn-secondary mt-3">Kembali ke Kelola Petugas</a>
</div>

<?php include('../includes/footer.php'); ?>