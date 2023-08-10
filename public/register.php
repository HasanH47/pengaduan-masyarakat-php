<?php
include('../classes/Database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nik = $_POST['nik'];
  $nama = $_POST['nama'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Enkripsi password menggunakan hash
  $telp = $_POST['telp'];

  // Validasi input
  $error_message = "";
  if (!preg_match('/^[0-9]{1,16}$/', $nik)) {
    $error_message .= "NIK hanya boleh berisi angka dan maksimal 16 digit. ";
  }
  if (!preg_match('/^[A-Za-z\s]+$/', $nama)) {
    $error_message .= "Nama hanya boleh berisi huruf saja. ";
  }
  if (!preg_match('/^[0-9]{10,13}$/', $telp)) {
    $error_message .= "Nomor telepon hanya boleh berisi angka dan memiliki panjang 10 hingga 13 digit. ";
  }

  if ($error_message === "") {
    // Implementasi validasi atau proses registrasi di sini (sesuai dengan metode Anda)
    $db = new Database();
    $conn = $db->getConnection();

    $query = "INSERT INTO masyarakat (nik, nama, username, password, telp) VALUES ('$nik', '$nama', '$username', '$hashedPassword', '$telp')";

    if ($conn->query($query)) {
      header('Location: login.php'); // Redirect kembali ke halaman login setelah registrasi berhasil
      exit();
    } else {
      $error_message = "Registrasi gagal. Silakan coba lagi.";
    }
  }
}
?>

<?php include('../includes/header.php'); ?>

<div class="container mt-5">
  <h2>Registrasi Pengguna</h2>
  <?php if (!empty($error_message)) { ?>
    <div class="alert alert-danger" role="alert">
      <?php echo $error_message; ?>
    </div>
  <?php } ?>
  <form method="post">
    <div class="mb-3">
      <label for="nik" class="form-label">NIK</label>
      <input type="text" class="form-control" id="nik" name="nik" pattern="[0-9]{1,16}" title="NIK hanya boleh berisi angka dan maksimal 16 digit" required maxlength="16">
    </div>
    <div class="mb-3">
      <label for="nama" class="form-label">Nama</label>
      <input type="text" class="form-control" id="nama" name="nama" pattern="[A-Za-z\s]+" title="Nama hanya boleh berisi huruf saja" required>
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
    <button type="submit" class="btn btn-primary">Register</button>
    <p>Sudah punya akun? <a href="login.php">Login</p>
  </form>
</div>

<?php include('../includes/footer.php'); ?>