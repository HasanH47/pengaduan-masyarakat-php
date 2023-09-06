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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $start_date = $_POST['start_date'];
  $end_date = $_POST['end_date'];

  $query = "SELECT * FROM pengaduan WHERE tgl_pengaduan BETWEEN ? AND ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param('ss', $start_date, $end_date);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows > 0) {
    $complaints = $result->fetch_all(MYSQLI_ASSOC);
  }
}
?>

<?php include('../includes/header.php'); ?>

<div class="container mt-5">
  <h2>Generate Laporan Pengaduan</h2>
  <form method="post">
    <div class="mb-3">
      <label for="start_date" class="form-label">Tanggal Mulai</label>
      <input type="date" class="form-control" id="start_date" name="start_date" required>
    </div>
    <div class="mb-3">
      <label for="end_date" class="form-label">Tanggal Selesai</label>
      <input type="date" class="form-control" id="end_date" name="end_date" required>
    </div>
    <button type="submit" class="btn btn-primary">Generate Laporan</button>
  </form>

  <?php if (isset($complaints)) { ?>
    <h3>Laporan Pengaduan Tanggal <?php echo $start_date; ?> hingga <?php echo $end_date; ?></h3>
    <div id="pdf-content">
      <table class="table">
        <thead>
          <tr>
            <th>ID Pengaduan</th>
            <th>Tanggal Pengaduan</th>
            <th>NIK</th>
            <th>Isi Laporan</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php $counter = 1;
          foreach ($complaints as $complaint) { ?>
            <tr>
              <td><?php echo $counter; ?></td>
              <td><?php echo $complaint['tgl_pengaduan']; ?></td>
              <td><?php echo $complaint['nik']; ?></td>
              <td><?php echo $complaint['isi_laporan']; ?></td>
              <td><?php echo ($complaint['status'] === '0' ? 'Belum Diproses' : ($complaint['status'] === 'proses' ? 'Sedang Diproses' : 'Selesai')); ?></td>
            </tr>
          <?php
            $counter++;
          } ?>
        </tbody>
      </table>
    </div>
    <br>
    <button class="btn btn-primary" onclick="printPDF()">Cetak PDF</button>
    <br>
  <?php } ?>
  <br>
  <a href="index.php" class="btn btn-primary">Kembali</a>
</div>

<script>
  function printPDF() {
    var printContent = document.getElementById("pdf-content").innerHTML;
    var originalContent = document.body.innerHTML;
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalContent;
  }
</script>

<?php include('../includes/footer.php'); ?>