<?php
session_start();
include('../classes/Database.php');

if (!isset($_SESSION['id_petugas']) || $_SESSION['level'] !== 'admin') {
  header('Location: ../public/login.php');
  exit();
}

if (!isset($_GET['id'])) {
  header('Location: verify_complaints.php');
  exit();
}

$id_pengaduan = $_GET['id'];
$db = new Database();
$conn = $db->getConnection();

// Ambil data pengaduan dan tanggapan jika ada
$query = "SELECT p.*, t.tanggapan, t.tgl_tanggapan, petugas.nama_petugas FROM pengaduan p LEFT JOIN tanggapan t ON p.id_pengaduan = t.id_pengaduan INNER JOIN petugas ON t.id_petugas = petugas.id_petugas WHERE p.id_pengaduan = $id_pengaduan";
$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
  header('Location: verify_complaints.php');
  exit();
}

$row = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $tanggapan = $_POST['tanggapan'];
  $id_petugas = $_SESSION['id_petugas'];

  // Implementasi validasi atau proses penyimpanan tanggapan di sini (sesuai dengan metode Anda)
  $query = "INSERT INTO tanggapan (id_pengaduan, tgl_tanggapan, tanggapan, id_petugas) VALUES ('$id_pengaduan', NOW(), '$tanggapan', '$id_petugas')";

  if ($conn->query($query)) {
    // Update status pengaduan menjadi 'proses'
    $updateQuery = "UPDATE pengaduan SET status = 'proses' WHERE id_pengaduan = $id_pengaduan";
    $conn->query($updateQuery);

    header('Location: verify_complaints.php'); // Redirect kembali ke halaman verifikasi pengaduan setelah memberikan tanggapan
    exit();
  } else {
    $error_message = "Gagal memberikan tanggapan. Silakan coba lagi.";
  }
}
?>

<?php include('../includes/header.php'); ?>

<div class="container mt-5">
  <h2>Tanggapi Pengaduan</h2>
  <?php if (isset($error_message)) { ?>
    <div class="alert alert-danger" role="alert">
      <?php echo $error_message; ?>
    </div>
  <?php } ?>

  <h4>Detail Pengaduan</h4>
  <p><strong>ID Pengaduan:</strong> <?php echo $row['id_pengaduan']; ?></p>
  <p><strong>Tanggal Pengaduan:</strong> <?php echo date('d F Y', strtotime($row['tgl_pengaduan'])); ?></p>
  <p><strong>Isi Laporan:</strong> <?php echo $row['isi_laporan']; ?></p>
  <p><strong>Status:</strong> <?php echo $row['status']; ?></p>

  <?php if ($row['tanggapan']) { ?>
    <h4>Tanggapan Sebelumnya</h4>
    <p><strong>Tanggal Tanggapan:</strong> <?php echo date('d F Y', strtotime($row['tgl_tanggapan'])); ?></p>
    <p><strong>Nama Petugas:</strong> <?php echo $row['nama_petugas']; ?></p>
    <p><?php echo $row['tanggapan']; ?></p>
  <?php } ?>

  <hr>

  <form method="post">
    <div class="mb-3">
      <label for="tanggapan" class="form-label">Tanggapan</label>
      <textarea class="form-control" id="tanggapan" name="tanggapan" rows="5" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Submit Tanggapan</button>
  </form>
</div>

<?php include('../includes/footer.php'); ?>