<?php
session_start();
include('../classes/Database.php');

if (!isset($_SESSION['nik'])) {
  header('Location: login.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_pengaduan = $_POST['id_pengaduan'];
  $isi_laporan = $_POST['isi_laporan'];
  $old_foto = $_POST['old_foto'];

  $db = new Database();
  $conn = $db->getConnection();

  $file_name = $_FILES['foto']['name'];
  $file_tmp = $_FILES['foto']['tmp_name'];
  $file_type = $_FILES['foto']['type'];

  // Tentukan lokasi penyimpanan foto
  $upload_dir = '../assets/uploads/';
  $file_path = $upload_dir . $file_name;

  // Pindahkan foto ke lokasi penyimpanan, jika ada foto yang diupload
  if (!empty($file_name)) {
    move_uploaded_file($file_tmp, $file_path);
    // Hapus foto lama jika ada
    if (!empty($old_foto)) {
      unlink($upload_dir . $old_foto);
    }
  } else {
    $file_name = $old_foto; // Gunakan nama foto lama jika tidak ada foto baru diupload
  }

  $query = "UPDATE pengaduan SET isi_laporan = '$isi_laporan', foto = '$file_name' WHERE id_pengaduan = $id_pengaduan";

  if ($conn->query($query)) {
    header('Location: dashboard.php');
    exit();
  } else {
    $error_message = "Gagal mengubah pengaduan. Silakan coba lagi.";
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


<?php include('../includes/header.php'); ?>

<div class="container mt-5">
  <h2>Edit Pengaduan</h2>
  <?php if (isset($error_message)) { ?>
    <div class="alert alert-danger" role="alert">
      <?php echo $error_message; ?>
    </div>
  <?php } ?>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="id_pengaduan" value="<?php echo $pengaduan['id_pengaduan']; ?>">
    <div class="mb-3">
      <label for="isi_laporan" class="form-label">Isi Laporan</label>
      <textarea class="form-control" id="isi_laporan" name="isi_laporan" rows="5" required><?php echo $pengaduan['isi_laporan']; ?></textarea>
    </div>
    <div class="mb-3">
      <label for="foto" class="form-label">Foto</label>
      <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
      <input type="hidden" name="old_foto" value="<?php echo $pengaduan['foto']; ?>">
    </div>
    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
  </form>
  <br>
  <a href="dashboard.php" class="btn btn-primary">Kembali</a>
</div>

<?php include('../includes/footer.php'); ?>