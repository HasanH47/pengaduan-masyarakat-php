<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aplikasi Pengaduan Masyarakat</title>
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
      <a class="navbar-brand" href="../public/index.php">Aplikasi Pengaduan Masyarakat</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="../public/index.php">Home</a>
          </li>
          <?php
          if (isset($_SESSION['level'])) {
            if ($_SESSION['level'] === 'admin') {
              echo '<li class="nav-item">
            <a class="nav-link" href="../admin/index.php">Dashboard</a>
          </li>';
            } elseif ($_SESSION['level'] === 'petugas') {
              echo '<li class="nav-item">
            <a class="nav-link" href="../officer/index.php">Dashboard</a>
          </li>';
            }
          }
          ?>
          <?php
          if (isset($_SESSION['user']) || isset($_SESSION['nama_petugas'])) {
            // Jika pengguna sudah login, tampilkan tautan "Logout"
            echo '<li class="nav-item">
                        <a class="nav-link" href="../public/logout.php">Logout</a>
                    </li>';
          }
          ?>
          <!-- Tambahkan tautan lain sesuai kebutuhan -->
        </ul>
      </div>
    </div>
  </nav>