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


/* TAMBAH PRODUK */

if(isset($_POST['simpan'])){

    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    mysqli_query($koneksi,
        "INSERT INTO produk
        (nama_produk, harga, stok)
        VALUES
        ('$nama', '$harga', '$stok')"
    );

    header("location:produk.php");
    exit;

}


/* HAPUS PRODUK */

if(isset($_GET['hapus'])){

    $id = $_GET['hapus'];

    mysqli_query($koneksi,
        "DELETE FROM produk
        WHERE id_produk='$id'"
    );

    header("location:produk.php");
    exit;

}


/* EDIT PRODUK */

if(isset($_POST['edit'])){

    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    mysqli_query($koneksi,
        "UPDATE produk SET
        nama_produk='$nama',
        harga='$harga',
        stok='$stok'
        WHERE id_produk='$id'"
    );

    header("location:produk.php");
    exit;

}


/* DATA EDIT */

$data_edit = null;

if(isset($_GET['edit'])){

    $id = $_GET['edit'];

    $query_edit = mysqli_query($koneksi,
        "SELECT * FROM produk
        WHERE id_produk='$id'"
    );

    $data_edit = mysqli_fetch_assoc($query_edit);

}


/* SEARCH PRODUK */

$cari = isset($_GET['cari']) ? $_GET['cari'] : '';

if($cari != ''){

    $produk = mysqli_query($koneksi,

        "SELECT * FROM produk
         WHERE nama_produk LIKE '%$cari%'
         ORDER BY id_produk DESC"

    );

}else{

    $produk = mysqli_query($koneksi,

        "SELECT * FROM produk
         ORDER BY id_produk DESC"

    );

}

?>


<!DOCTYPE html>

<html>

<head>

<title>Produk - Sweet Dessert</title>


<link rel="stylesheet"
href="../assets/bootstrap-5.3.8-dist/bootstrap-5.3.8-dist/css/bootstrap.min.css">


<link rel="stylesheet"
href="../assets/bootstrap-icons/bootstrap-icons.css">

</head>


<body style="background-color:#fff0f6;">


<div class="container-fluid">

<div class="row min-vh-100">


    <!-- SIDEBAR -->

    <div class="col-md-3 col-lg-2 p-0">

        <div class="d-flex flex-column p-3 h-100 shadow"
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
                       class="nav-link text-white">

                        <i class="bi bi-house-door me-2"></i>
                        Dashboard

                    </a>

                </li>


                <li class="nav-item">

                    <a href="produk.php"
                       class="nav-link active bg-white"
                       style="color:#d63384;">

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



    <!-- CONTENT -->

    <div class="col-md-9 col-lg-10 p-4">


        <!-- HEADER -->

        <div class="card border-0 shadow-sm rounded-4 mb-4">

            <div class="card-body p-4">

                <h3 class="fw-bold mb-1"
                    style="color:#d63384;">

                    Data Produk

                </h3>

                <p class="text-secondary mb-0">

                    Kelola data produk Sweet Dessert

                </p>

            </div>

        </div>



        <!-- FORM -->

        <div class="card border-0 shadow-sm rounded-4 mb-4">

            <div class="card-body p-4">

                <h5 class="fw-bold mb-3">

                    <?php

                    if($data_edit){

                        echo "Edit Produk";

                    }else{

                        echo "Tambah Produk";

                    }

                    ?>

                </h5>


                <form method="post">

                    <?php if($data_edit){ ?>

                        <input type="hidden"
                               name="id"
                               value="<?php echo $data_edit['id_produk']; ?>">

                    <?php } ?>


                    <div class="row g-3">


                        <div class="col-md-4">

                            <label class="form-label">
                                Nama Produk
                            </label>

                            <input type="text"
                                   name="nama"
                                   class="form-control"
                                   placeholder="Masukkan nama produk"
                                   value="<?php

                                   if($data_edit){

                                       echo $data_edit['nama_produk'];

                                   }

                                   ?>"
                                   required>

                        </div>


                        <div class="col-md-4">

                            <label class="form-label">
                                Harga
                            </label>

                            <input type="number"
                                   name="harga"
                                   class="form-control"
                                   placeholder="Masukkan harga"
                                   value="<?php

                                   if($data_edit){

                                       echo $data_edit['harga'];

                                   }

                                   ?>"
                                   required>

                        </div>


                        <div class="col-md-4">

                            <label class="form-label">
                                Stok
                            </label>

                            <input type="number"
                                   name="stok"
                                   class="form-control"
                                   placeholder="Masukkan stok"
                                   value="<?php

                                   if($data_edit){

                                       echo $data_edit['stok'];

                                   }

                                   ?>"
                                   required>

                        </div>


                        <div class="col-12">

                            <?php if($data_edit){ ?>

                                <button type="submit"
                                        name="edit"
                                        class="btn"
                                        style="background-color:#d63384;color:white;">

                                    <i class="bi bi-pencil me-1"></i>
                                    Update

                                </button>


                                <a href="produk.php"
                                   class="btn btn-secondary">

                                    Batal

                                </a>

                            <?php }else{ ?>

                                <button type="submit"
                                        name="simpan"
                                        class="btn"
                                        style="background-color:#d63384;color:white;">

                                    <i class="bi bi-plus-lg me-1"></i>
                                    Simpan

                                </button>

                            <?php } ?>

                        </div>

                    </div>

                </form>

            </div>

        </div>



        <!-- SEARCH -->

        <form method="GET" class="mb-3">

            <div class="input-group">

                <input type="text"
                       name="cari"
                       class="form-control"
                       placeholder="Cari nama produk..."
                       value="<?php echo $cari; ?>">


                <button type="submit"
                        class="btn"
                        style="background-color:#d63384;color:white;">

                    <i class="bi bi-search me-2"></i>
                    Cari

                </button>


                <a href="produk.php"
                   class="btn btn-secondary">

                    Reset

                </a>

            </div>

        </form>



        <!-- TABEL PRODUK -->

        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-body p-4">

                <h5 class="fw-bold mb-3">

                    Daftar Produk

                </h5>


                <div class="table-responsive">

                    <table class="table table-bordered align-middle">


                        <thead style="background-color:#f8a8c4;">

                            <tr>

                                <th>No</th>
                                <th>Nama Produk</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th>Aksi</th>

                            </tr>

                        </thead>


                        <tbody>


                            <?php

                            $no = 1;

                            while($d = mysqli_fetch_assoc($produk)){

                            ?>

                            <tr>


                                <td>

                                    <?php echo $no++; ?>

                                </td>


                                <td>

                                    <?php echo $d['nama_produk']; ?>

                                </td>


                                <td>

                                    Rp <?php echo number_format($d['harga'],0,',','.'); ?>

                                </td>


                                <td>

                                    <?php echo $d['stok']; ?>

                                </td>


                                <td>


                                    <a href="produk.php?edit=<?php echo $d['id_produk']; ?>"
                                       class="btn btn-sm"
                                       style="background-color:#ffc107;color:white;">

                                        <i class="bi bi-pencil"></i>
                                        Edit

                                    </a>


                                    <a href="produk.php?hapus=<?php echo $d['id_produk']; ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Yakin ingin menghapus produk ini?');">

                                        <i class="bi bi-trash"></i>
                                        Hapus

                                    </a>


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