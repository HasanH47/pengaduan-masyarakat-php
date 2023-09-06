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

// Mengambil daftar pengaduan yang belum diverifikasi atau sedang diproses
$queryUnverified = "SELECT * FROM pengaduan WHERE status = '0' OR status = 'proses' ORDER BY tgl_pengaduan DESC";
$resultUnverified = $conn->query($queryUnverified);

$complaints = [];

if ($resultUnverified && $resultUnverified->num_rows > 0) {
  $complaints = $resultUnverified->fetch_all(MYSQLI_ASSOC);
}

// Verifikasi pengaduan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['verify'])) {
    $id_pengaduan = $_POST['id_pengaduan'];
    $queryVerify = "UPDATE pengaduan SET status = 'proses' WHERE id_pengaduan = $id_pengaduan";
    $conn->query($queryVerify);
  }
}
?>

<?php include('../includes/header.php'); ?>

<div class="container mt-5">
  <h2>Verifikasi Pengaduan</h2>
  <?php if (!empty($complaints)) { ?>
    <table class="table">
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal Pengaduan</th>
          <th>NIK</th>
          <th>Isi Laporan</th>
          <th>Foto</th>
          <th>Status</th>
          <th>Jumlah Tanggapan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $counter = 1;
        foreach ($complaints as $complaint) {
          $id_pengaduan = $complaint['id_pengaduan'];
          $queryTanggapan = "SELECT COUNT(*) AS jumlah_tanggapan FROM tanggapan WHERE id_pengaduan = $id_pengaduan";
          $resultTanggapan = $conn->query($queryTanggapan);
          $rowTanggapan = $resultTanggapan->fetch_assoc();
        ?>
          <tr>
            <td><?php echo $counter; ?></td>
            <td><?php echo $complaint['tgl_pengaduan']; ?></td>
            <td><?php echo $complaint['nik']; ?></td>
            <td><?php echo $complaint['isi_laporan']; ?></td>
            <td><?php echo ($complaint['foto'] ? 'Ada' : 'Tidak Ada'); ?></td>
            <td><?php echo getStatusLabel($complaint['status']); ?></td>
            <td><span class="badge bg-danger"><?php echo $rowTanggapan['jumlah_tanggapan']; ?></span></td>
            <td>
              <?php if ($complaint['status'] === 'proses') { ?>
                <button class="btn btn-success disabled">Terverifikasi</button>
              <?php } elseif ($complaint['status'] === '0') { ?>
                <form method="post">
                  <input type="hidden" name="id_pengaduan" value="<?php echo $complaint['id_pengaduan']; ?>">
                  <button type="submit" name="verify" class="btn btn-success">Verifikasi</button>
                </form>
              <?php } ?>
              <br>
              <?php if ($complaint['status'] === 'proses') { ?>
                <a href="respond_complaint.php?id=<?php echo $complaint['id_pengaduan']; ?>" class="btn btn-primary btn-sm">Tanggapi</a>
              <?php } ?>
            </td>
          </tr>
        <?php
          $counter++;
        }
        ?>
      </tbody>
    </table>
  <?php } else { ?>
    <p>Tidak ada pengaduan yang perlu diverifikasi atau sedang diproses saat ini.</p>
  <?php } ?>
  <a href="index.php" class="btn btn-primary">Kembali</a>
</div>

<?php include('../includes/footer.php'); ?>

<?php
function getStatusLabel($status)
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