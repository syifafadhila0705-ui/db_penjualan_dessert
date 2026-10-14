<?php

session_start();

include "../koneksi.php";


/* CEK LOGIN */

if(!isset($_SESSION['login'])){
    header("location:../login.php");
    exit;
}

if($_SESSION['level'] != "kasir"){
    header("location:../login.php");
    exit;
}


/* TAMBAH PELANGGAN */

if(isset($_POST['simpan'])){

    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];

    mysqli_query($koneksi,
        "INSERT INTO pelanggan
        (nama_pelanggan, alamat, no_hp)
        VALUES
        ('$nama', '$alamat', '$no_hp')"
    );

    header("location:pelanggan.php");
    exit;
}


/* HAPUS PELANGGAN */

if(isset($_GET['hapus'])){

    $id = $_GET['hapus'];

    $cek = mysqli_query($koneksi,
        "SELECT * FROM penjualan
         WHERE id_pelanggan='$id'"
    );

    if(mysqli_num_rows($cek) > 0){

        echo "<script>
                alert('Pelanggan tidak bisa dihapus karena sudah memiliki transaksi!');
                window.location='pelanggan.php';
              </script>";

        exit;

    }else{

        mysqli_query($koneksi,
            "DELETE FROM pelanggan
             WHERE id_pelanggan='$id'"
        );

        echo "<script>
                alert('Pelanggan berhasil dihapus!');
                window.location='pelanggan.php';
              </script>";

        exit;
    }
}


/* EDIT PELANGGAN */

if(isset($_POST['edit'])){

    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];

    mysqli_query($koneksi,
        "UPDATE pelanggan SET
        nama_pelanggan='$nama',
        alamat='$alamat',
        no_hp='$no_hp'
        WHERE id_pelanggan='$id'"
    );

    header("location:pelanggan.php");
    exit;
}


/* CARI PELANGGAN */

$cari = isset($_GET['cari']) ? $_GET['cari'] : '';

if($cari != ''){

    $pelanggan = mysqli_query($koneksi,
        "SELECT * FROM pelanggan
        WHERE nama_pelanggan LIKE '%$cari%'
        ORDER BY id_pelanggan DESC"
    );

}else{

    $pelanggan = mysqli_query($koneksi,
        "SELECT * FROM pelanggan
        ORDER BY id_pelanggan DESC"
    );

}


/* DATA EDIT */

$edit_data = null;

if(isset($_GET['edit'])){

    $id = $_GET['edit'];

    $hasil_edit = mysqli_query($koneksi,
        "SELECT * FROM pelanggan
        WHERE id_pelanggan='$id'"
    );

    $edit_data = mysqli_fetch_assoc($hasil_edit);
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Kelola Pelanggan - Sweet Dessert</title>

    <link rel="stylesheet"
          href="../assets/bootstrap-5.3.8-dist/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <link rel="stylesheet"
          href="../assets/bootstrap-icons/bootstrap-icons.css">

</head>


<body style="background-color:#fff0f6;">

<div class="container-fluid">

    <div class="row min-vh-100 g-0">


        <!-- SIDEBAR -->

        <div class="col-md-3 col-lg-2 p-0">

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

                    <li class="nav-item">

                        <a href="index.php"
                           class="nav-link text-white">

                            <i class="bi bi-house-door me-2"></i>

                            Dashboard

                        </a>

                    </li>


                    <li class="nav-item">

                        <a href="pelanggan.php"
                           class="nav-link active bg-white"
                           style="color:#d63384;">

                            <i class="bi bi-people me-2"></i>

                            Pelanggan

                        </a>

                    </li>


                    <li class="nav-item">

                        <a href="penjualan.php"
                           class="nav-link text-white">

                            <i class="bi bi-cart-check me-2"></i>

                            Penjualan

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

        <div class="col-md-9 col-lg-10 p-4">


            <h3 class="fw-bold mb-1">
                Kelola Pelanggan
            </h3>

            <p class="text-secondary mb-4">
                Kelola data pelanggan Sweet Dessert
            </p>



            <!-- FORM TAMBAH / EDIT -->

            <div class="card border-0 shadow-sm rounded-4 mb-4">

                <div class="card-body p-4">

                    <?php if($edit_data != null){ ?>


                        <h5 class="fw-bold mb-3"
                            style="color:#d63384;">

                            Edit Pelanggan

                        </h5>


                        <form method="POST">

                            <input type="hidden"
                                   name="id"
                                   value="<?php echo $edit_data['id_pelanggan']; ?>">


                            <!-- NAMA -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Nama Pelanggan
                                </label>

                                <input type="text"
                                       name="nama"
                                       class="form-control"
                                       value="<?php echo $edit_data['nama_pelanggan']; ?>"
                                       required>

                            </div>


                            <!-- ALAMAT -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Alamat
                                </label>

                                <textarea name="alamat"
                                          class="form-control"
                                          rows="3"
                                          required><?php echo $edit_data['alamat']; ?></textarea>

                            </div>


                            <!-- NO HP -->

                            <div class="mb-3">

                                <label class="form-label">
                                    No HP
                                </label>

                                <input type="text"
                                       name="no_hp"
                                       class="form-control"
                                       value="<?php echo $edit_data['no_hp']; ?>"
                                       maxlength="13"
                                       oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                       required>

                            </div>


                            <button type="submit"
                                    name="edit"
                                    class="btn"
                                    style="background-color:#d63384;color:white;">

                                <i class="bi bi-pencil me-2"></i>

                                Simpan Perubahan

                            </button>


                            <a href="pelanggan.php"
                               class="btn btn-secondary">

                                Batal

                            </a>

                        </form>


                    <?php }else{ ?>


                        <h5 class="fw-bold mb-3"
                            style="color:#d63384;">

                            Tambah Pelanggan

                        </h5>


                        <form method="POST">


                            <!-- NAMA -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Nama Pelanggan
                                </label>

                                <input type="text"
                                       name="nama"
                                       class="form-control"
                                       placeholder="Masukkan nama pelanggan"
                                       required>

                            </div>


                            <!-- ALAMAT -->

                            <div class="mb-3">

                                <label class="form-label">
                                    Alamat
                                </label>

                                <textarea name="alamat"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Masukkan alamat pelanggan"
                                          required></textarea>

                            </div>


                            <!-- NO HP -->

                            <div class="mb-3">

                                <label class="form-label">
                                    No HP
                                </label>

                                <input type="text"
                                       name="no_hp"
                                       class="form-control"
                                       placeholder="Masukkan nomor HP"
                                       maxlength="13"
                                       oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                       required>

                            </div>


                            <button type="submit"
                                    name="simpan"
                                    class="btn"
                                    style="background-color:#d63384;color:white;">

                                <i class="bi bi-plus-lg me-2"></i>

                                Simpan

                            </button>

                        </form>


                    <?php } ?>

                </div>

            </div>



            <!-- DATA PELANGGAN -->

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body p-4">


                    <h5 class="fw-bold mb-3"
                        style="color:#d63384;">

                        Data Pelanggan

                    </h5>


                    <!-- SEARCH -->

                    <form method="GET" class="mb-3">

                        <div class="input-group">

                            <input type="text"
                                   name="cari"
                                   class="form-control"
                                   placeholder="Cari nama pelanggan..."
                                   value="<?php echo $cari; ?>">

                            <button type="submit"
                                    class="btn"
                                    style="background-color:#d63384;color:white;">

                                <i class="bi bi-search me-2"></i>

                                Cari

                            </button>


                            <a href="pelanggan.php"
                               class="btn btn-secondary">

                                Reset

                            </a>

                        </div>

                    </form>



                    <!-- TABEL -->

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead>

                                <tr>

                                    <th width="60">
                                        No
                                    </th>

                                    <th>
                                        Nama Pelanggan
                                    </th>

                                    <th>
                                        Alamat
                                    </th>

                                    <th>
                                        No HP
                                    </th>

                                    <th width="180">
                                        Aksi
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php

                                $no = 1;

                                while($d = mysqli_fetch_assoc($pelanggan)){

                                ?>

                                <tr>

                                    <td>
                                        <?php echo $no++; ?>
                                    </td>

                                    <td>
                                        <?php echo $d['nama_pelanggan']; ?>
                                    </td>

                                    <td>
                                        <?php echo $d['alamat']; ?>
                                    </td>

                                    <td>
                                        <?php echo $d['no_hp']; ?>
                                    </td>

                                    <td>

                                        <a href="pelanggan.php?edit=<?php echo $d['id_pelanggan']; ?>"
                                           class="btn btn-sm"
                                           style="background-color:#ffc107;color:white;">

                                            <i class="bi bi-pencil"></i>

                                            Edit

                                        </a>


                                        <a href="pelanggan.php?hapus=<?php echo $d['id_pelanggan']; ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Yakin ingin menghapus pelanggan ini?');">

                                            <i class="bi bi-trash"></i>

                                            Hapus

                                        </a>

                                    </td>

                                </tr>

                                <?php } ?>

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