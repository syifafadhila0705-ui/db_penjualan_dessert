<?php

session_start();

include "../koneksi.php";


/* CEK LOGIN */

if(!isset($_SESSION['login'])){

    header("location:../login.php");
    exit;

}


/* CEK ROLE */

if($_SESSION['level'] != "kasir"){

    header("location:../login.php");
    exit;

}


/* TOTAL PELANGGAN */

$query_pelanggan = mysqli_query($koneksi,
    "SELECT * FROM pelanggan"
);

$total_pelanggan = mysqli_num_rows($query_pelanggan);


/* TOTAL PENJUALAN */

$query_penjualan = mysqli_query($koneksi,
    "SELECT * FROM penjualan"
);

$total_penjualan = mysqli_num_rows($query_penjualan);

?>


<!DOCTYPE html>

<html>

<head>

    <title>Dashboard Kasir - Sweet Dessert</title>


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

                        Dashboard Kasir

                    </small>

                </div>


                <hr class="text-white">


                <ul class="nav nav-pills flex-column gap-2">


                    <!-- DASHBOARD -->

                    <li class="nav-item">

                        <a href="index.php"
                           class="nav-link active bg-white"
                           style="color:#d63384;">

                            <i class="bi bi-house-door me-2"></i>

                            Dashboard

                        </a>

                    </li>


                    <!-- PELANGGAN -->

                    <li class="nav-item">

                        <a href="pelanggan.php"
                           class="nav-link text-white">

                            <i class="bi bi-people me-2"></i>

                            Pelanggan

                        </a>

                    </li>


                    <!-- PENJUALAN -->

                    <li class="nav-item">

                        <a href="penjualan.php"
                           class="nav-link text-white">

                            <i class="bi bi-cart-check me-2"></i>

                            Penjualan

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


            <!-- HEADER -->

            <h3 class="fw-bold mb-1">

                Dashboard Kasir

            </h3>


            <p class="text-secondary mb-4">

                Selamat datang di Sweet Dessert

            </p>



            <!-- CARD -->

            <div class="row g-4">


                <!-- PELANGGAN -->

                <div class="col-md-6">

                    <div class="card border-0 shadow-sm p-4">


                        <div class="d-flex align-items-center">


                            <div class="me-3">

                                <i class="bi bi-people fs-1"
                                   style="color:#d63384;"></i>

                            </div>


                            <div>

                                <h6 class="text-secondary mb-1">

                                    Total Pelanggan

                                </h6>


                                <h2 class="fw-bold mb-0"
                                    style="color:#d63384;">

                                    <?php echo $total_pelanggan; ?>

                                </h2>

                            </div>


                        </div>


                    </div>

                </div>



                <!-- PENJUALAN -->

                <div class="col-md-6">

                    <div class="card border-0 shadow-sm p-4">


                        <div class="d-flex align-items-center">


                            <div class="me-3">

                                <i class="bi bi-cart-check fs-1"
                                   style="color:#d63384;"></i>

                            </div>


                            <div>

                                <h6 class="text-secondary mb-1">

                                    Total Penjualan

                                </h6>


                                <h2 class="fw-bold mb-0"
                                    style="color:#d63384;">

                                    <?php echo $total_penjualan; ?>

                                </h2>

                            </div>


                        </div>


                    </div>

                </div>


            </div>


        </div>


    </div>

</div>


</body>

</html>