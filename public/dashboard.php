<?php
session_start();
include('../classes/Database.php');

if (!isset($_SESSION['nik'])) {
  header('Location: login.php');
  exit();
}

$nik = $_SESSION['nik'];
$db = new Database();
$conn = $db->getConnection();

$query = "SELECT pengaduan.*, COUNT(tanggapan.id_tanggapan) AS jumlah_tanggapan 
          FROM pengaduan 
          LEFT JOIN tanggapan ON pengaduan.id_pengaduan = tanggapan.id_pengaduan 
          WHERE pengaduan.nik = '$nik'
          GROUP BY pengaduan.id_pengaduan";
$result = $conn->query($query);
?>

<?php include('../includes/header.php'); ?>

<div class="container mt-5">
  <h2>Dashboard Pengaduan</h2>
  <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['user']); ?></p>
  <a href="submit_complaint.php" class="btn btn-primary">Kirim Pengaduan</a>

  <h3>Riwayat Pengaduan Anda</h3>
  <table class="table">
    <thead>
      <tr>
        <th>No</th>
        <th>Tanggal Pengaduan</th>
        <th>Isi Laporan</th>
        <th>Status</th>
        <th>Jumlah Tanggapan</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $counter = 1;
      while ($row = $result->fetch_assoc()) {
        $status = $row['status'];
        $countTanggapan = $row['jumlah_tanggapan'];
      ?>
        <tr>
          <td><?php echo $counter; ?></td>
          <td><?php echo date('d F Y', strtotime($row['tgl_pengaduan'])); ?></td>
          <td><?php echo htmlspecialchars($row['isi_laporan']); ?></td>
          <td><?php
              if ($status === '0') {
                echo 'Belum Diproses';
              } elseif ($status === 'proses') {
                echo 'Sedang Diproses';
              } elseif ($status === 'selesai') {
                echo 'Selesai';
              } else {
                echo 'Status Tidak Valid';
              }
              ?>
          </td>
          <td><span class="badge bg-danger"><?php echo $countTanggapan; ?></span></td>
          <td>
            <?php if ($status === '0') { ?>
              <a href="edit_complaint.php?id=<?php echo $row['id_pengaduan']; ?>" class="btn btn-warning btn-sm">Edit</a>
              <form action="delete_complaint.php" method="post" style="display: inline-block;">
                <input type="hidden" name="id_pengaduan" value="<?php echo $row['id_pengaduan']; ?>">
                <input type="hidden" name="foto" value="<?php echo $row['foto']; ?>">
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pengaduan ini?')">Delete</button>
              </form>
            <?php } else { ?>
              <button class="btn btn-warning btn-sm" disabled>Edit</button>
              <button class="btn btn-danger btn-sm" disabled>Delete</button>
            <?php } ?>
            <a href="detail_complaint.php?id=<?php echo $row['id_pengaduan']; ?>" class="btn btn-info btn-sm">Detail</a>
          </td>
        </tr>
      <?php $counter++;
      } ?>
    </tbody>
  </table>
</div>

<?php include('../includes/footer.php'); ?>