<?php

session_start();

include "../koneksi.php";


/* CEK LOGIN */

if(!isset($_SESSION['login'])){

    header("location:../login.php");
    exit;

}


/* CEK ROLE */

if($_SESSION['level'] != "admin"){

    header("location:../login.php");
    exit;

}


/* JUMLAH PRODUK */

$produk = mysqli_query($koneksi, "SELECT * FROM produk");
$jumlah_produk = mysqli_num_rows($produk);

/* TRANSAKSI TERBARU */

$transaksi = mysqli_query($koneksi, "
    SELECT 
        penjualan.id_penjualan,
        penjualan.tanggal,
        pelanggan.nama_pelanggan,
        produk.nama_produk,
        detail_penjualan.jumlah,
        penjualan.total
    FROM penjualan
    JOIN pelanggan 
        ON penjualan.id_pelanggan = pelanggan.id_pelanggan
    JOIN detail_penjualan 
        ON penjualan.id_penjualan = detail_penjualan.id_penjualan
    JOIN produk 
        ON detail_penjualan.id_produk = produk.id_produk
    ORDER BY penjualan.id_penjualan DESC
    LIMIT 5
");

?>

<!DOCTYPE html>
<html>
<head>

    <title>Dashboard Admin - Sweet Dessert</title>

    <!-- Bootstrap Lokal -->
    <link rel="stylesheet" href="../assets/bootstrap-5.3.8-dist/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <!-- Bootstrap Icons Lokal -->
    <link rel="stylesheet" href="../assets/bootstrap-icons/bootstrap-icons.css">

</head>

<body style="background-color:#fff0f6;">

<div class="d-flex">


    <!-- SIDEBAR -->

<div class="col-md-3 col-lg-2 p-0 min-vh-100">

    <div class="d-flex flex-column p-3 h-100 shadow min-vh-100"
         style="background-color:#f8a8c4;">

        <div class="text-center mb-4">

            <h4 class="fw-bold text-white mb-1">
                Sweet Dessert
            </h4>

            <small class="text-white">
                Dashboard Admin
            </small>

        </div>


        <hr class="text-white">


        <ul class="nav nav-pills flex-column gap-2">

            <li class="nav-item">

                <a href="index.php"
                   class="nav-link active bg-white"
                   style="color:#d63384;">

                    <i class="bi bi-house-door me-2"></i>
                    Dashboard

                </a>

            </li>


            <li class="nav-item">

                <a href="produk.php"
                   class="nav-link text-white">

                    <i class="bi bi-box-seam me-2"></i>
                    Kelola Produk

                </a>

            </li>


            <li class="nav-item">

                <a href="laporan.php"
                   class="nav-link text-white">

                    <i class="bi bi-file-earmark-text me-2"></i>
                    Laporan

                </a>

            </li>

        </ul>


        <div class="mt-auto">

            <hr class="text-white">

            <a href="../logout.php"
               class="btn btn-light w-100"
               style="color:#d63384;">

                <i class="bi bi-box-arrow-right me-2"></i>
                Logout

            </a>

        </div>

    </div>

</div>

    <!-- KONTEN -->

    <div class="p-4 flex-grow-1">


        

<div class="card border-0 shadow-sm rounded-4 mb-4">

    <div class="card-body p-4">

        <h3 class="fw-bold mb-1"
            style="color:#d63384;">

            Dashboard Admin

        </h3>

        <p class="text-secondary mb-0">

            Selamat datang di Sweet Dessert

        </p>

    </div>

</div>


        <!-- CARD -->

        <div class="row g-4">


            <!-- CARD PRODUK -->

            <div class="col-md-6">

                <div class="card border-0 shadow-sm rounded-4 p-4">

                    <div class="d-flex align-items-center">

                        <div class="rounded-3 p-3 me-3"
                             style="background-color:#f8a8c4; color:white;">

                            <i class="bi bi-box-seam fs-2"></i>

                        </div>

                        <div>

                            <p class="text-muted mb-1">

                                Total Produk

                            </p>

                            <h2 class="fw-bold mb-0">

                                <?php echo $jumlah_produk; ?>

                            </h2>

                        </div>

                    </div>

                </div>

            </div>


            <!-- CARD LAPORAN -->

            <div class="col-md-6">

                <div class="card border-0 shadow-sm rounded-4 p-4">

                    <div class="d-flex align-items-center">

                        <div class="rounded-3 p-3 me-3"
                             style="background-color:#d63384; color:white;">

                            <i class="bi bi-file-earmark-text fs-2"></i>

                        </div>

                        <div>

                            <p class="text-muted mb-1">

                                Laporan Penjualan

                            </p>

                            <h2 class="fw-bold mb-0">

                                <i class="bi bi-clipboard-data"></i>

                            </h2>

                        </div>

                    </div>

                </div>

            </div>

            <!-- TRANSAKSI TERBARU -->

<div class="card border-0 shadow-sm rounded-4 mt-4">

    <div class="card-body p-4">

        <h5 class="fw-bold mb-3">

            <i class="bi bi-clock-history me-2"
               style="color:#d63384;"></i>

            Transaksi Terbaru

        </h5>


        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Total</th>

                    </tr>

                </thead>


                <tbody>

                    <?php

                    $no = 1;

                    while($d = mysqli_fetch_assoc($transaksi)){

                    ?>

                    <tr>

                        <td>
                            <?php echo $no++; ?>
                        </td>

                        <td>
                            <?php echo $d['tanggal']; ?>
                        </td>

                        <td>
                            <?php echo $d['nama_pelanggan']; ?>
                        </td>

                        <td>
                            <?php echo $d['nama_produk']; ?>
                        </td>

                        <td>
                            <?php echo $d['jumlah']; ?>
                        </td>

                        <td>
                            Rp <?php echo number_format($d['total'], 0, ',', '.'); ?>
                        </td>

                    </tr>

                    <?php

                    }

                    ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


        </div>

    </div>

</div>

</body>
</html>