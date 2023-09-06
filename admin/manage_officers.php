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

// Ambil daftar petugas dari database
$query = "SELECT * FROM petugas";
$result = $conn->query($query);
?>

<?php include('../includes/header.php'); ?>

<div class="container mt-5">
  <h2>Kelola Petugas</h2>
  <a href="add_officer.php" class="btn btn-primary mb-3">Tambah Petugas</a>
  <table class="table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Username</th>
        <th>Level</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php $counter = 1;
      while ($row = $result->fetch_assoc()) { ?>
        <tr>
          <td><?php echo $counter; ?></td>
          <td><?php echo $row['nama_petugas']; ?></td>
          <td><?php echo $row['username']; ?></td>
          <td><?php echo ucfirst($row['level']); ?></td>
          <td>
            <?php if ($row['level'] !== 'admin') { ?>
              <a href="edit_officer.php?id=<?php echo $row['id_petugas']; ?>" class="btn btn-warning btn-sm">Edit</a>
              <a href="delete_officer.php?id=<?php echo $row['id_petugas']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus petugas ini?')">Hapus</a>
            <?php } else { ?>
              <button class="btn btn-warning btn-sm" disabled>Edit</button>
              <button class="btn btn-danger btn-sm" disabled>Hapus</button>
            <?php } ?>
          </td>
        </tr>
      <?php $counter++;
      } ?>
    </tbody>
  </table>
</div>

<?php include('../includes/footer.php'); ?>