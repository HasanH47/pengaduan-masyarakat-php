<?php
session_start();
include('../classes/Database.php');

$error_message = "";

if (!isset($_SESSION['nik'])) {
  header('Location: login.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tgl_pengaduan = date('Y-m-d');
  $nik = $_SESSION['nik'];
  $isi_laporan = $_POST['isi_laporan'];
  $status = '0'; // Default status

  $file_name = $_FILES['foto']['name'];
  $file_tmp = $_FILES['foto']['tmp_name'];
  $file_type = $_FILES['foto']['type'];

  // Tentukan lokasi penyimpanan foto
  $upload_dir = '../assets/uploads/';
  $file_path = $upload_dir . $file_name;

  // Pindahkan foto ke lokasi penyimpanan
  if (!empty($file_name)) {
    move_uploaded_file($file_tmp, $file_path);

    // Implementasi validasi atau proses penyimpanan laporan pengaduan di sini (sesuai dengan metode Anda)
    $db = new Database();
    $conn = $db->getConnection();

    $query = "INSERT INTO pengaduan (tgl_pengaduan, nik, isi_laporan, foto, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $tgl_pengaduan, $nik, $isi_laporan, $file_name, $status);

    if ($stmt->execute()) {
      header('Location: dashboard.php'); // Redirect kembali ke halaman dashboard setelah pengaduan berhasil dikirim
      exit();
    } else {
      $error_message = "Gagal mengirimkan pengaduan. Silakan coba lagi.";
    }

    $stmt->close();
  } else {
    $error_message = "Mohon pilih foto untuk pengaduan Anda.";
  }
}
?>

<?php include('../includes/header.php'); ?>

<div class="container mt-5">
  <h2>Submit Pengaduan</h2>
  <?php if (!empty($error_message)) { ?>
    <div class="alert alert-danger" role="alert">
      <?php echo $error_message; ?>
    </div>
  <?php } ?>
  <form method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="isi_laporan" class="form-label">Isi Laporan</label>
      <textarea class="form-control" id="isi_laporan" name="isi_laporan" rows="5" required></textarea>
    </div>
    <div class="mb-3">
      <label for="foto" class="form-label">Foto</label>
      <input type="file" class="form-control" id="foto" name="foto" accept="image/*" required>
    </div>
    <button type="submit" class="btn btn-primary">Submit Pengaduan</button>
  </form>
</div>

<?php include('../includes/footer.php'); ?>