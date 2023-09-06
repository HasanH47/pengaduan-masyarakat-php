<?php
session_start();
include('../classes/Database.php');

if (!isset($_SESSION['id_petugas']) || $_SESSION['level'] !== 'admin') {
  header('Location: ../public/login.php');
  exit();
}

$db = new Database();
$conn = $db->getConnection();

// Ambil daftar pengaduan yang telah ditanggapi oleh petugas
$query = "
  SELECT pengaduan.*, tanggapan.tgl_tanggapan, tanggapan.tanggapan AS isi_tanggapan
  FROM pengaduan
  INNER JOIN tanggapan ON pengaduan.id_pengaduan = tanggapan.id_pengaduan
  WHERE tanggapan.id_petugas
  ORDER BY tanggapan.tgl_tanggapan DESC
";

$result = $conn->query($query);
?>

<?php include('../includes/header.php'); ?>

<div class="container mt-5">
  <h2>Pengaduan Ditanggapi oleh <?php echo $_SESSION['nama_petugas']; ?></h2>
  <table class="table">
    <thead>
      <tr>
        <th>#</th>
        <th>Tanggal Pengaduan</th>
        <th>Isi Laporan</th>
        <th>Foto</th>
        <th>Tanggal Tanggapan</th>
        <th>Status</th>
        <th>Jumlah Tanggapan</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $counter = 1; // Counter untuk nomor pengaduan
      while ($row = $result->fetch_assoc()) {
        $queryCountTanggapan = "SELECT COUNT(*) AS jumlah_tanggapan FROM tanggapan WHERE id_pengaduan = {$row['id_pengaduan']}";
        $resultCountTanggapan = $conn->query($queryCountTanggapan);
        $countTanggapan = $resultCountTanggapan->fetch_assoc()['jumlah_tanggapan'];
      ?>
        <tr>
          <td><?php echo $counter; ?></td>
          <td><?php echo $row['tgl_pengaduan']; ?></td>
          <td><?php echo $row['isi_laporan']; ?></td>
          <td><?php echo ($row['foto'] ? 'Ada' : 'Tidak Ada'); ?></td>
          <td><?php echo $row['tgl_tanggapan']; ?></td>
          <td><?php echo getStatusLabel($row['status']); ?></td>
          <td><span class="badge bg-danger"><?php echo $countTanggapan; ?></span></td>
          <td>
            <a href="detail_complaint.php?id=<?php echo $row['id_pengaduan']; ?>" class="btn btn-info btn-sm">Detail</a>
            <?php if ($row['status'] !== 'selesai') { ?>
              <form action="update_status.php" method="post" style="display: inline-block;">
                <input type="hidden" name="id_pengaduan" value="<?php echo $row['id_pengaduan']; ?>">
                <select name="status">
                  <option value="selesai">Selesai</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
              </form>
            <?php } ?>
          </td>
        </tr>
      <?php
        $counter++;
      }
      ?>
    </tbody>
  </table>
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