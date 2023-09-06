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

if (!isset($_GET['id'])) {
  header('Location: dashboard.php');
  exit();
}

$id_pengaduan = $_GET['id'];

// Query untuk mengambil detail pengaduan dan tanggapan oleh petugas
$query = "
  SELECT pengaduan.*, tanggapan.tgl_tanggapan, tanggapan.tanggapan AS isi_tanggapan, petugas.nama_petugas
  FROM pengaduan
  INNER JOIN tanggapan ON pengaduan.id_pengaduan = tanggapan.id_pengaduan
  INNER JOIN petugas ON tanggapan.id_petugas = petugas.id_petugas
  WHERE pengaduan.id_pengaduan = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id_pengaduan);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
  header('Location: dashboard.php');
  exit();
}

$row = $result->fetch_assoc();

include('../includes/header.php');
?>

<div class="container mt-5">
  <h2>Detail Pengaduan</h2>
  <div class="card mb-3">
    <div class="card-body">
      <h5 class="card-title">ID Pengaduan: <?php echo $row['id_pengaduan']; ?></h5>
      <p class="card-text">Tanggal Pengaduan: <?php echo $row['tgl_pengaduan']; ?></p>
      <p class="card-text">Isi Laporan: <?php echo $row['isi_laporan']; ?></p>
      <p class="card-text">Status: <?php echo $row['status']; ?></p>
      <?php if ($row['foto']) { ?>
        <p class="card-text">Gambar:</p>
        <img src="../assets/uploads/<?php echo $row['foto']; ?>" alt="Foto Pengaduan" class="img-fluid">
      <?php } ?>
    </div>
  </div>
  <h3>Tanggapan oleh Petugas <?php echo $row['nama_petugas']; ?></h3>
  <div class="card mb-3">
    <div class="card-body">
      <p class="card-text">Tanggal Tanggapan: <?php echo $row['tgl_tanggapan']; ?></p>
      <p class="card-text">Isi Tanggapan: <?php echo $row['isi_tanggapan']; ?></p>
    </div>
  </div>
  <a href="responded_complaints.php" class="btn btn-primary">Kembali</a>
</div>

<?php include('../includes/footer.php'); ?>