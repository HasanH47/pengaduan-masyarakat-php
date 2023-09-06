<?php
session_start();
include('../classes/Database.php');

if (!isset($_SESSION['id_petugas']) || $_SESSION['level'] !== 'petugas') {
  header('Location: ../public/login.php');
  exit();
}

$db = new Database();
$conn = $db->getConnection();

$query = "SELECT * FROM pengaduan WHERE status = '0' OR status = 'proses' ORDER BY tgl_pengaduan DESC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
  $complaints = $result->fetch_all(MYSQLI_ASSOC);
}

// Verifikasi pengaduan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['verify'])) {
    $id_pengaduan = $_POST['id_pengaduan'];
    $queryVerify = "UPDATE pengaduan SET status = 'proses' WHERE id_pengaduan = $id_pengaduan";
    $conn->query($queryVerify);
    header('Location: verify_complaints.php'); // Redirect kembali ke halaman verifikasi pengaduan setelah verifikasi
    exit();
  }
}
?>

<?php include('../includes/header.php'); ?>

<div class="container mt-5">
  <h2>Verifikasi Pengaduan</h2>
  <?php if (isset($complaints)) { ?>
    <table class="table">
      <thead>
        <tr>
          <th>ID Pengaduan</th>
          <th>Tanggal Pengaduan</th>
          <th>Isi Laporan</th>
          <th>Foto</th>
          <th>Status</th>
          <th>Tanggapan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $counter = 1;
        foreach ($complaints as $row) {
          $id_pengaduan = $row['id_pengaduan'];
          $queryTanggapan = "SELECT COUNT(*) AS jumlah_tanggapan FROM tanggapan WHERE id_pengaduan = $id_pengaduan";
          $resultTanggapan = $conn->query($queryTanggapan);
          $rowTanggapan = $resultTanggapan->fetch_assoc();
        ?>
          <tr>
            <td><?php echo $counter; ?></td>
            <td><?php echo date('d F Y', strtotime($row['tgl_pengaduan'])); ?></td>
            <td><?php echo $row['isi_laporan']; ?></td>
            <td><?php echo ($row['foto'] ? 'Ada' : 'Tidak Ada'); ?></td>
            <td><?php
                if ($row['status'] === '0') {
                  echo 'Belum Diproses';
                } elseif ($row['status'] === 'proses') {
                  echo 'Sedang Diproses';
                } elseif ($row['status'] === 'selesai') {
                  echo 'Selesai';
                } else {
                  echo 'Status Tidak Valid';
                }
                ?>
            </td>
            <td><span class="badge bg-danger"><?php echo $rowTanggapan['jumlah_tanggapan']; ?></span></td>
            <td>
              <?php if ($row['status'] === 'proses') { ?>
                <button class="btn btn-success disabled">Terverifikasi</button>
                <br>
              <?php } elseif ($row['status'] === '0') { ?>
                <form method="post">
                  <input type="hidden" name="id_pengaduan" value="<?php echo $row['id_pengaduan']; ?>">
                  <button type="submit" name="verify" class="btn btn-success">Verifikasi</button>
                </form>
              <?php } ?>
              <br>
              <?php if ($row['status'] === '0') { ?>
                <button class="btn btn-primary btn-sm disabled">Tanggapi</button>
              <?php } elseif ($row['status'] === 'proses') { ?>
                <a href="respond_complaint.php?id=<?php echo $row['id_pengaduan']; ?>" class="btn btn-primary btn-sm">Tanggapi</a>
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
    <p>Tidak ada pengaduan yang perlu diverfikasi saat ini.</p>
  <?php } ?>
  <a href="index.php" class="btn btn-primary">Kembali</a>
</div>

<?php include('../includes/footer.php'); ?>