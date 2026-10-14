<?php

session_start();

include "../koneksi.php";


/* CEK LOGIN */

if(!isset($_SESSION['login'])){

    header("location:../login.php");
    exit;

}


/* CEK ADMIN */

if($_SESSION['level'] != "admin"){

    header("location:../index.php");
    exit;

}


/* FILTER TANGGAL */

$dari = isset($_GET['dari']) ? $_GET['dari'] : '';

$sampai = isset($_GET['sampai']) ? $_GET['sampai'] : '';


/* TOTAL HARI INI */

$hari_ini = date('Y-m-d');

$total_hari_ini = mysqli_query($koneksi, "

    SELECT SUM(total) AS total

    FROM penjualan

    WHERE tanggal = '$hari_ini'

");

$data_hari_ini = mysqli_fetch_assoc($total_hari_ini);

$total_hari_ini = $data_hari_ini['total'] ?? 0;


/* TOTAL BULAN INI */

$bulan_ini = date('Y-m');

$total_bulan_ini = mysqli_query($koneksi, "

    SELECT SUM(total) AS total

    FROM penjualan

    WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$bulan_ini'

");

$data_bulan_ini = mysqli_fetch_assoc($total_bulan_ini);

$total_bulan_ini = $data_bulan_ini['total'] ?? 0;


/* TOTAL KESELURUHAN */

$total_semua = mysqli_query($koneksi, "

    SELECT SUM(total) AS total

    FROM penjualan

");

$data_semua = mysqli_fetch_assoc($total_semua);

$total_semua = $data_semua['total'] ?? 0;


/* QUERY LAPORAN */

$query = "

    SELECT
        penjualan.id_penjualan,
        penjualan.tanggal,
        pelanggan.nama_pelanggan,
        produk.nama_produk,
        detail_penjualan.jumlah,
        produk.harga,
        penjualan.total

    FROM penjualan

    JOIN pelanggan
        ON penjualan.id_pelanggan = pelanggan.id_pelanggan

    JOIN detail_penjualan
        ON penjualan.id_penjualan = detail_penjualan.id_penjualan

    JOIN produk
        ON detail_penjualan.id_produk = produk.id_produk

";


/* JIKA ADA FILTER */

if($dari != '' && $sampai != ''){

    $query .= "

        WHERE penjualan.tanggal
        BETWEEN '$dari' AND '$sampai'

    ";

}


$query .= "

    ORDER BY penjualan.id_penjualan DESC

";


$laporan = mysqli_query($koneksi, $query);

?>


<!DOCTYPE html>
<html>

<head>

    <title>Laporan Penjualan - Sweet Dessert</title>


    <link rel="stylesheet"
          href="../assets/bootstrap-5.3.8-dist/bootstrap-5.3.8-dist/css/bootstrap.min.css">


    <link rel="stylesheet"
          href="../assets/bootstrap-icons/bootstrap-icons.css">

</head>


<body style="background-color:#fff0f6;">


<div class="container-fluid p-0">


    <div class="row min-vh-100 g-0">


        <!-- SIDEBAR -->

        <div class="col-md-3 col-lg-2">


            <div class="d-flex flex-column p-3 h-100 min-vh-100 shadow"
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


                    <!-- DASHBOARD -->

                    <li class="nav-item">

                        <a href="index.php"
                           class="nav-link text-white">

                            <i class="bi bi-house-door me-2"></i>

                            Dashboard

                        </a>

                    </li>


                    <!-- PRODUK -->

                    <li class="nav-item">

                        <a href="produk.php"
                           class="nav-link text-white">

                            <i class="bi bi-box-seam me-2"></i>

                            Kelola Produk

                        </a>

                    </li>


                    <!-- LAPORAN -->

                    <li class="nav-item">

                        <a href="laporan.php"
                           class="nav-link active bg-white"
                           style="color:#d63384;">

                            <i class="bi bi-file-earmark-text me-2"></i>

                            Laporan

                        </a>

                    </li>


                </ul>


                <!-- LOGOUT -->

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



        <!-- CONTENT -->

        <div class="col-md-9 col-lg-10 p-4">


            <h3 class="fw-bold mb-1">

                Laporan Penjualan

            </h3>


            <p class="text-secondary mb-4">

                Data penjualan Sweet Dessert

            </p>



            <!-- TOTAL -->

            <div class="row g-4 mb-4">


                <!-- HARI INI -->

                <div class="col-md-4">

                    <div class="card border-0 shadow-sm p-4">

                        <h6 class="text-secondary">

                            Total Hari Ini

                        </h6>


                        <h4 class="fw-bold"
                            style="color:#d63384;">

                            Rp <?php echo number_format($total_hari_ini, 0, ',', '.'); ?>

                        </h4>

                    </div>

                </div>



                <!-- BULAN INI -->

                <div class="col-md-4">

                    <div class="card border-0 shadow-sm p-4">

                        <h6 class="text-secondary">

                            Total Bulan Ini

                        </h6>


                        <h4 class="fw-bold"
                            style="color:#d63384;">

                            Rp <?php echo number_format($total_bulan_ini, 0, ',', '.'); ?>

                        </h4>

                    </div>

                </div>



                <!-- KESELURUHAN -->

                <div class="col-md-4">

                    <div class="card border-0 shadow-sm p-4">

                        <h6 class="text-secondary">

                            Total Keseluruhan

                        </h6>


                        <h4 class="fw-bold"
                            style="color:#d63384;">

                            Rp <?php echo number_format($total_semua, 0, ',', '.'); ?>

                        </h4>

                    </div>

                </div>


            </div>



            <!-- FILTER -->

            <div class="card border-0 shadow-sm mb-4">


                <div class="card-body">


                    <h5 class="fw-bold mb-3">

                        Filter Laporan

                    </h5>


                    <form method="GET">


                        <div class="row g-3 align-items-end">


                            <!-- DARI -->

                            <div class="col-md-4">

                                <label class="form-label">

                                    Dari Tanggal

                                </label>


                                <input type="date"
                                       name="dari"
                                       class="form-control"
                                       value="<?php echo $dari; ?>">

                            </div>



                            <!-- SAMPAI -->

                            <div class="col-md-4">

                                <label class="form-label">

                                    Sampai Tanggal

                                </label>


                                <input type="date"
                                       name="sampai"
                                       class="form-control"
                                       value="<?php echo $sampai; ?>">

                            </div>



                            <!-- BUTTON -->

                            <div class="col-md-4">

                                <button type="submit"
                                        class="btn"
                                        style="background-color:#d63384;color:white;">

                                    <i class="bi bi-search me-2"></i>

                                    Cari Laporan

                                </button>


                                <a href="laporan.php"
                                   class="btn btn-secondary">

                                    Reset

                                </a>

                            </div>


                        </div>


                    </form>


                </div>

            </div>



            <!-- TABEL LAPORAN -->

            <div class="card border-0 shadow-sm">


                <div class="card-body">


                    <div class="table-responsive">


                        <table class="table table-bordered table-hover align-middle">


                            <thead style="background-color:#f8a8c4;">

                                <tr>

                                    <th>No</th>

                                    <th>Tanggal</th>

                                    <th>Pelanggan</th>

                                    <th>Produk</th>

                                    <th>Jumlah</th>

                                    <th>Harga</th>

                                    <th>Total</th>

                                </tr>

                            </thead>


                            <tbody>


                            <?php

                            $no = 1;


                            while($d = mysqli_fetch_assoc($laporan)){

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

                                        Rp <?php echo number_format($d['harga'], 0, ',', '.'); ?>

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