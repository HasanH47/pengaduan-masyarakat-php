<?php
session_start();
include('../classes/Database.php');

if (!isset($_SESSION['nik'])) {
  header('Location: login.php');
  exit();
}

if (!isset($_GET['id'])) {
  header('Location: dashboard.php');
  exit();
}

$id_pengaduan = $_GET['id'];
$db = new Database();
$conn = $db->getConnection();

$queryPengaduan = "SELECT * FROM pengaduan WHERE id_pengaduan = $id_pengaduan AND nik = '{$_SESSION['nik']}'";
$resultPengaduan = $conn->query($queryPengaduan);

if (!$resultPengaduan || $resultPengaduan->num_rows === 0) {
  header('Location: dashboard.php');
  exit();
}

$rowPengaduan = $resultPengaduan->fetch_assoc();

// Query untuk mengambil komentar atau tanggapan terkait dengan pengaduan
$queryKomentar = "SELECT * FROM tanggapan WHERE id_pengaduan = $id_pengaduan";
$resultKomentar = $conn->query($queryKomentar);
?>

<?php include('../includes/header.php'); ?>

<div class="container mt-5">
  <h2>Detail Pengaduan</h2>
  <div class="card mb-3">
    <div class="card-body">
      <h5 class="card-title">ID Pengaduan: <?php echo $rowPengaduan['id_pengaduan']; ?></h5>
      <p class="card-text">Tanggal Pengaduan: <?php echo date('d F Y', strtotime($rowPengaduan['tgl_pengaduan'])); ?></p>
      <p class="card-text">Isi Laporan: <?php echo htmlspecialchars($rowPengaduan['isi_laporan']); ?></p>
      <p class="card-text">Status: <?php echo getStatusText($rowPengaduan['status']); ?></p>
      <?php if ($rowPengaduan['foto']) { ?>
        <p class="card-text">Gambar:</p>
        <img src="../assets/uploads/<?php echo $rowPengaduan['foto']; ?>" alt="Foto Pengaduan" class="img-fluid">
      <?php } ?>
    </div>
  </div>

  <h3>Tanggapan</h3>
  <?php while ($rowKomentar = $resultKomentar->fetch_assoc()) { ?>
    <div class="card mb-3">
      <div class="card-body">
        <p class="card-text">Tanggal Tanggapan: <?php echo date('d F Y', strtotime($rowKomentar['tgl_tanggapan'])); ?></p>
        <p class="card-text">Tanggapan: <?php echo htmlspecialchars($rowKomentar['tanggapan']); ?></p>
      </div>
    </div>
  <?php } ?>

  <a href="dashboard.php" class="btn btn-primary">Kembali</a>
</div>

<?php include('../includes/footer.php'); ?>

<?php
function getStatusText($status)
{
  if ($status === '0') {
    return 'Belum Diproses';
  } elseif ($status === 'proses') {
    return 'Sedang Diproses';
  } elseif ($status === 'selesai') {
    return 'Selesai';
  } else {
    return 'Status Tidak Valid';
  }
}
?>