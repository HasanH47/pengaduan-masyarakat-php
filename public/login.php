<?php
session_start();
include('../classes/Database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $db = new Database();
  $conn = $db->getConnection();

  // Periksa ke tabel masyarakat
  $queryMasyarakat = "SELECT * FROM masyarakat WHERE username = ?";
  $stmtMasyarakat = $conn->prepare($queryMasyarakat);
  $stmtMasyarakat->bind_param("s", $username);
  $stmtMasyarakat->execute();
  $resultMasyarakat = $stmtMasyarakat->get_result();

  if ($resultMasyarakat && $resultMasyarakat->num_rows > 0) {
    $rowMasyarakat = $resultMasyarakat->fetch_assoc();
    if (password_verify($password, $rowMasyarakat['password'])) {
      $_SESSION['user'] = $username;
      $_SESSION['nik'] = $rowMasyarakat['nik'];
      header('Location: dashboard.php');
      exit();
    }
  }

  // Periksa ke tabel petugas
  $queryPetugas = "SELECT * FROM petugas WHERE username = ?";
  $stmtPetugas = $conn->prepare($queryPetugas);
  $stmtPetugas->bind_param("s", $username);
  $stmtPetugas->execute();
  $resultPetugas = $stmtPetugas->get_result();

  if ($resultPetugas && $resultPetugas->num_rows > 0) {
    $rowPetugas = $resultPetugas->fetch_assoc();
    if (password_verify($password, $rowPetugas['password'])) {
      $_SESSION['id_petugas'] = $rowPetugas['id_petugas'];
      $_SESSION['nama_petugas'] = $rowPetugas['nama_petugas'];
      $_SESSION['level'] = $rowPetugas['level'];

      if ($rowPetugas['level'] === 'admin') {
        header('Location: ../admin/index.php');
      } else if ($rowPetugas['level'] === 'petugas') {
        header('Location: ../officer/index.php');
      }
      exit();
    }
  }

  $error_message = "Username atau password salah. Silakan coba lagi.";
}
?>

<?php include('../includes/header.php'); ?>

<div class="container mt-5">
  <h2>Login Pengguna</h2>
  <?php if (isset($error_message)) { ?>
    <div class="alert alert-danger" role="alert">
      <?php echo $error_message; ?>
    </div>
  <?php } ?>
  <form method="post">
    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Login</button>
    <p>Belum punya akun? <a href="register.php">Register</a></p>
  </form>
</div>

<?php include('../includes/footer.php'); ?>