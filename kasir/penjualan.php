<?php
session_start();
include "../koneksi.php";

if(!isset($_SESSION['login'])){
    header("location:../login.php");
    exit;
}

if($_SESSION['level'] != "kasir"){
    header("location:../login.php");
    exit;
}


/* =========================
   TAMBAH KOLOM PEMBAYARAN
========================= */

$cek_metode = mysqli_query($koneksi,
    "SHOW COLUMNS FROM penjualan LIKE 'metode_pembayaran'"
);

if(mysqli_num_rows($cek_metode) == 0){

    mysqli_query($koneksi,
        "ALTER TABLE penjualan
         ADD metode_pembayaran VARCHAR(20) NOT NULL DEFAULT 'Cash'"
    );

}


$cek_bayar = mysqli_query($koneksi,
    "SHOW COLUMNS FROM penjualan LIKE 'uang_bayar'"
);

if(mysqli_num_rows($cek_bayar) == 0){

    mysqli_query($koneksi,
        "ALTER TABLE penjualan
         ADD uang_bayar DECIMAL(15,2) NOT NULL DEFAULT 0"
    );

}


$cek_kembalian = mysqli_query($koneksi,
    "SHOW COLUMNS FROM penjualan LIKE 'kembalian'"
);

if(mysqli_num_rows($cek_kembalian) == 0){

    mysqli_query($koneksi,
        "ALTER TABLE penjualan
         ADD kembalian DECIMAL(15,2) NOT NULL DEFAULT 0"
    );

}


/* =========================
   CETAK STRUK
========================= */

if(isset($_GET['cetak'])){

    $id = $_GET['cetak'];

    $data = mysqli_query($koneksi,
        "SELECT
            penjualan.id_penjualan,
            penjualan.tanggal,
            penjualan.total,
            penjualan.metode_pembayaran,
            penjualan.uang_bayar,
            penjualan.kembalian,
            pelanggan.nama_pelanggan
        FROM penjualan
        JOIN pelanggan
            ON penjualan.id_pelanggan = pelanggan.id_pelanggan
        WHERE penjualan.id_penjualan='$id'"
    );

    $penjualan_cetak = mysqli_fetch_assoc($data);

    if(!$penjualan_cetak){
        echo "Data penjualan tidak ditemukan!";
        exit;
    }

    $detail_cetak = mysqli_query($koneksi,
        "SELECT
            produk.nama_produk,
            produk.harga,
            detail_penjualan.jumlah
        FROM detail_penjualan
        JOIN produk
            ON detail_penjualan.id_produk = produk.id_produk
        WHERE detail_penjualan.id_penjualan='$id'
        ORDER BY detail_penjualan.id_produk ASC"
    );
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Struk Penjualan - Sweet Dessert</title>

<link rel="stylesheet"
href="../assets/bootstrap-5.3.8-dist/bootstrap-5.3.8-dist/css/bootstrap.min.css">

</head>

<body>

<div class="container mt-4">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card">

<div class="card-body">

<h3 class="text-center fw-bold">
Sweet Dessert
</h3>

<p class="text-center mb-4">
Struk Penjualan
</p>


<table class="table table-borderless">

<tr>

<td width="35%">
No Transaksi
</td>

<td>
: <?php echo $penjualan_cetak['id_penjualan']; ?>
</td>

</tr>


<tr>

<td>
Tanggal
</td>

<td>
: <?php echo $penjualan_cetak['tanggal']; ?>
</td>

</tr>


<tr>

<td>
Pelanggan
</td>

<td>
: <?php echo $penjualan_cetak['nama_pelanggan']; ?>
</td>

</tr>

</table>


<table class="table table-bordered">

<thead>

<tr>

<th>Produk</th>

<th>Harga</th>

<th>Jumlah</th>

<th>Subtotal</th>

</tr>

</thead>


<tbody>

<?php

while($d = mysqli_fetch_assoc($detail_cetak)){

    $subtotal = $d['harga'] * $d['jumlah'];

?>

<tr>

<td>
<?php echo $d['nama_produk']; ?>
</td>

<td>
Rp <?php echo number_format($d['harga'],0,',','.'); ?>
</td>

<td>
<?php echo $d['jumlah']; ?>
</td>

<td>
Rp <?php echo number_format($subtotal,0,',','.'); ?>
</td>

</tr>

<?php } ?>


<tr>

<th colspan="3" class="text-end">
TOTAL
</th>

<th>

Rp <?php
echo number_format(
    $penjualan_cetak['total'],
    0,
    ',',
    '.'
);
?>

</th>

</tr>

</tbody>

</table>


<!-- =========================
     PEMBAYARAN DI STRUK
========================= -->

<table class="table table-borderless mb-2">

<tr>

<td width="50%">
Metode Pembayaran
</td>

<td class="text-end fw-semibold">

<?php echo $penjualan_cetak['metode_pembayaran']; ?>

</td>

</tr>


<tr>

<td>
Uang Bayar
</td>

<td class="text-end">

Rp <?php
echo number_format(
    $penjualan_cetak['uang_bayar'],
    0,
    ',',
    '.'
);
?>

</td>

</tr>


<tr>

<td>
Kembalian
</td>

<td class="text-end fw-bold">

Rp <?php
echo number_format(
    $penjualan_cetak['kembalian'],
    0,
    ',',
    '.'
);
?>

</td>

</tr>

</table>


<p class="text-center mt-4">
Terima kasih telah membeli di Sweet Dessert
</p>


<!-- TOMBOL TIDAK IKUT PRINT -->

<div class="text-center d-print-none">

<button onclick="window.print()"
class="btn btn-primary">

Cetak Struk

</button>


<a href="penjualan.php"
class="btn btn-secondary">

Kembali

</a>

</div>


</div>

</div>

</div>

</div>

</div>


</body>

</html>

<?php
    exit;
}


/* =========================
   SIMPAN PENJUALAN
========================= */

if(isset($_POST['simpan'])){

    $id_pelanggan = $_POST['id_pelanggan'];
    $id_produk = $_POST['id_produk'];
    $jumlah = $_POST['jumlah'];
    $tanggal = date('Y-m-d');

    /* DATA PEMBAYARAN */

    $metode_pembayaran = $_POST['metode_pembayaran'];

    $uang_bayar = isset($_POST['uang_bayar'])
        ? $_POST['uang_bayar']
        : 0;


    if(empty($id_pelanggan)){

        echo "<script>
                alert('Pilih nama pelanggan terlebih dahulu!');
                window.location='penjualan.php';
              </script>";

        exit;
    }


    if(empty($id_produk)){

        echo "<script>
                alert('Pilih produk terlebih dahulu!');
                window.location='penjualan.php';
              </script>";

        exit;
    }


    $total = 0;


    /* CEK PRODUK DAN STOK */

    for($i = 0; $i < count($id_produk); $i++){

        $produk_id = $id_produk[$i];
        $qty = $jumlah[$i];

        if($produk_id == ""){
            continue;
        }


        if($qty <= 0){

            echo "<script>
                    alert('Jumlah produk harus lebih dari 0!');
                    window.location='penjualan.php';
                  </script>";

            exit;
        }


        $data_produk = mysqli_query($koneksi,
            "SELECT * FROM produk
             WHERE id_produk='$produk_id'"
        );


        $produk = mysqli_fetch_assoc($data_produk);


        if(!$produk){

            echo "<script>
                    alert('Produk tidak ditemukan!');
                    window.location='penjualan.php';
                  </script>";

            exit;
        }


        if($qty > $produk['stok']){

            echo "<script>
                    alert('Stok ".$produk['nama_produk']." tidak cukup!');
                    window.location='penjualan.php';
                  </script>";

            exit;
        }


        $total += $produk['harga'] * $qty;

    }


    /* =========================
       CEK PEMBAYARAN
    ========================= */

    if($metode_pembayaran == "Cash"){

        if($uang_bayar < $total){

            echo "<script>
                    alert('Uang pembayaran kurang!');
                    window.location='penjualan.php';
                  </script>";

            exit;
        }

        $kembalian = $uang_bayar - $total;

    }else{

        /* QRIS */

        $uang_bayar = $total;

        $kembalian = 0;

    }


    /* SIMPAN PENJUALAN */

    mysqli_query($koneksi,
        "INSERT INTO penjualan
        (
            id_pelanggan,
            total,
            tanggal,
            metode_pembayaran,
            uang_bayar,
            kembalian
        )
        VALUES
        (
            '$id_pelanggan',
            '$total',
            '$tanggal',
            '$metode_pembayaran',
            '$uang_bayar',
            '$kembalian'
        )"
    );


    $id_penjualan = mysqli_insert_id($koneksi);


    /* SIMPAN DETAIL */

    for($i = 0; $i < count($id_produk); $i++){

        $produk_id = $id_produk[$i];
        $qty = $jumlah[$i];

        if($produk_id == ""){
            continue;
        }


        mysqli_query($koneksi,
            "INSERT INTO detail_penjualan
            (id_penjualan, id_produk, jumlah)
            VALUES
            ('$id_penjualan', '$produk_id', '$qty')"
        );


        /* KURANGI STOK */

        mysqli_query($koneksi,
            "UPDATE produk
             SET stok = stok - $qty
             WHERE id_produk='$produk_id'"
        );

    }


    echo "<script>
            alert('Penjualan berhasil disimpan!');
            window.location='penjualan.php';
          </script>";

    exit;
}


/* =========================
   DATA PELANGGAN
========================= */

$pelanggan = mysqli_query($koneksi,
    "SELECT * FROM pelanggan
     ORDER BY nama_pelanggan ASC"
);


/* =========================
   DATA PRODUK
========================= */

$produk_data = mysqli_query($koneksi,
    "SELECT * FROM produk
     WHERE stok > 0
     ORDER BY nama_produk ASC"
);


/* =========================
   SEARCH RIWAYAT
========================= */

$cari = "";

if(isset($_GET['cari'])){
    $cari = mysqli_real_escape_string(
        $koneksi,
        $_GET['cari']
    );
}


/* =========================
   RIWAYAT PENJUALAN
========================= */

$query_penjualan = "
    SELECT
        penjualan.id_penjualan,
        penjualan.tanggal,
        pelanggan.nama_pelanggan,
        penjualan.total,
        penjualan.metode_pembayaran,
        penjualan.uang_bayar,
        penjualan.kembalian,

        GROUP_CONCAT(
            CONCAT(
                produk.nama_produk,
                ' (',
                detail_penjualan.jumlah,
                'x)'
            )
            SEPARATOR ', '
        ) AS daftar_produk

    FROM penjualan

    JOIN pelanggan
        ON penjualan.id_pelanggan = pelanggan.id_pelanggan

    JOIN detail_penjualan
        ON penjualan.id_penjualan = detail_penjualan.id_penjualan

    JOIN produk
        ON detail_penjualan.id_produk = produk.id_produk

    GROUP BY
        penjualan.id_penjualan,
        penjualan.tanggal,
        pelanggan.nama_pelanggan,
        penjualan.total,
        penjualan.metode_pembayaran,
        penjualan.uang_bayar,
        penjualan.kembalian
";


/* JIKA ADA PENCARIAN */

if($cari != ""){

    $query_penjualan .= "

    HAVING
        pelanggan.nama_pelanggan LIKE '%$cari%'
        OR
        GROUP_CONCAT(produk.nama_produk)
        LIKE '%$cari%'
    ";

}


$query_penjualan .= "

    ORDER BY penjualan.id_penjualan DESC

    LIMIT 10
";


$penjualan = mysqli_query(
    $koneksi,
    $query_penjualan
);

?>


<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Penjualan - Sweet Dessert</title>

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
class="nav-link text-white">

<i class="bi bi-people me-2"></i>
Pelanggan

</a>

</li>


<li class="nav-item">

<a href="penjualan.php"
class="nav-link active bg-white"
style="color:#d63384;">

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


<!-- CONTENT -->

<div class="col-md-9 col-lg-10 p-4">


<h3 class="fw-bold mb-1">
Penjualan
</h3>

<p class="text-secondary mb-4">
Kelola transaksi penjualan Sweet Dessert
</p>


<!-- TAMBAH PENJUALAN -->

<div class="card border-0 shadow-sm rounded-4 mb-4">

<div class="card-body p-4">


<h5 class="fw-bold mb-4"
style="color:#d63384;">

Tambah Penjualan

</h5>


<form method="POST">


<!-- PELANGGAN -->

<div class="mb-4">

<label class="form-label fw-semibold">
Nama Pelanggan
</label>

<select name="id_pelanggan"
class="form-select"
required>

<option value="">
-- Pilih Nama Pelanggan --
</option>


<?php while($p = mysqli_fetch_assoc($pelanggan)){ ?>

<option value="<?php echo $p['id_pelanggan']; ?>">

<?php echo $p['nama_pelanggan']; ?>

</option>

<?php } ?>

</select>

</div>


<!-- PRODUK -->

<div class="table-responsive">

<table class="table table-bordered align-middle">

<thead class="table-light">

<tr>

<th width="5%">
No
</th>

<th width="35%">
Produk
</th>

<th width="20%">
Harga
</th>

<th width="15%">
Jumlah
</th>

<th width="15%">
Subtotal
</th>

<th width="10%">
Aksi
</th>

</tr>

</thead>


<tbody id="tabelProduk">


<tr class="barisProduk">


<td class="nomor">
1
</td>


<td>

<select name="id_produk[]"
class="form-select produk"
onchange="hitungBaris(this)"
required>

<option value="">
-- Pilih Produk --
</option>


<?php

mysqli_data_seek($produk_data, 0);

while($p = mysqli_fetch_assoc($produk_data)){

?>

<option
value="<?php echo $p['id_produk']; ?>"
data-harga="<?php echo $p['harga']; ?>"
data-stok="<?php echo $p['stok']; ?>">

<?php echo $p['nama_produk']; ?>

- Rp <?php
echo number_format(
    $p['harga'],
    0,
    ',',
    '.'
);
?>

(Stok <?php echo $p['stok']; ?>)

</option>

<?php } ?>

</select>

</td>


<td>

<input type="text"
class="form-control harga"
value="Rp 0"
readonly>

</td>


<td>

<input type="number"
name="jumlah[]"
class="form-control jumlah"
value="1"
min="1"
onchange="hitungBaris(this)"
oninput="hitungBaris(this)"
required>

</td>


<td>

<input type="text"
class="form-control subtotal"
value="Rp 0"
readonly>

</td>


<td>

<button type="button"
class="btn btn-danger"
onclick="hapusBaris(this)">

<i class="bi bi-trash"></i>

</button>

</td>

</tr>

</tbody>


<tfoot>

<tr>

<th colspan="4"
class="text-end">

Total

</th>

<th>

<input type="text"
id="total"
class="form-control fw-bold"
value="Rp 0"
readonly>

</th>

<th></th>

</tr>

</tfoot>

</table>

</div>


<button type="button"
class="btn btn-primary mb-3"
onclick="tambahProduk()">

<i class="bi bi-plus-lg me-1"></i>
Tambah Produk

</button>


<!-- =========================
     METODE PEMBAYARAN
========================= -->

<div class="row mt-2 mb-3">

<div class="col-md-4">

<label class="form-label fw-semibold">
Metode Pembayaran
</label>

<select name="metode_pembayaran"
id="metode_pembayaran"
class="form-select"
onchange="ubahPembayaran()"
required>

<option value="Cash">
Cash
</option>

<option value="QRIS">
QRIS
</option>

</select>

</div>


<div class="col-md-4"
id="kolomUangBayar">

<label class="form-label fw-semibold">
Uang Bayar
</label>

<input type="number"
name="uang_bayar"
id="uang_bayar"
class="form-control"
min="0"
placeholder="Masukkan uang bayar"
oninput="hitungKembalian()">

</div>


<div class="col-md-4">

<label class="form-label fw-semibold">
Kembalian
</label>

<input type="text"
id="kembalian"
class="form-control fw-bold"
value="Rp 0"
readonly>

</div>

</div>


<br>


<button type="submit"
name="simpan"
class="btn"
style="background-color:#d63384;color:white;">

<i class="bi bi-cart-check me-2"></i>
Simpan Penjualan

</button>


</form>

</div>

</div>


<!-- RIWAYAT -->

<div class="card border-0 shadow-sm rounded-4">

<div class="card-body p-4">


<h5 class="fw-bold mb-3"
style="color:#d63384;">

Riwayat Penjualan

</h5>


<!-- SEARCH -->

<form method="GET"
class="mb-3">

<div class="row g-2">

<div class="col-md-8">

<input type="text"
name="cari"
class="form-control"
placeholder="Cari nama pelanggan atau produk..."
value="<?php echo htmlspecialchars($cari); ?>">

</div>


<div class="col-md-2">

<button type="submit"
class="btn w-100"
style="background-color:#d63384;color:white;">

<i class="bi bi-search me-1"></i>
Cari

</button>

</div>


<div class="col-md-2">

<a href="penjualan.php"
class="btn btn-secondary w-100">

<i class="bi bi-arrow-counterclockwise me-1"></i>
Reset

</a>

</div>

</div>

</form>


<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-light">

<tr>

<th>No</th>

<th>Tanggal</th>

<th>Pelanggan</th>

<th style="width: 40%;">Produk yang Dibeli</th>

<th>Total</th>

<th>Pembayaran</th>

<th>Aksi</th>

</tr>

</thead>


<tbody>

<?php

$no = 1;

if(mysqli_num_rows($penjualan) > 0){

    while($d = mysqli_fetch_assoc($penjualan)){

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
<?php echo $d['daftar_produk']; ?>
</td>


<td>

Rp <?php

echo number_format(
    $d['total'],
    0,
    ',',
    '.'
);

?>

</td>


<!-- =========================
     PEMBAYARAN RIWAYAT
========================= -->

<td>

<div class="fw-semibold">

<?php echo $d['metode_pembayaran']; ?>

</div>


<?php if($d['metode_pembayaran'] == "Cash"){ ?>

<small class="text-secondary">

Bayar:
Rp <?php
echo number_format(
    $d['uang_bayar'],
    0,
    ',',
    '.'
);
?>

<br>

Kembali:
Rp <?php
echo number_format(
    $d['kembalian'],
    0,
    ',',
    '.'
);
?>

</small>

<?php }else{ ?>

<small class="text-secondary">
Pembayaran QRIS
</small>

<?php } ?>

</td>


<td>

<a href="penjualan.php?cetak=<?php echo $d['id_penjualan']; ?>"
class="btn btn-sm"
style="background-color:#d63384;color:white;">

<i class="bi bi-printer"></i>

</a>

</td>

</tr>

<?php

    }

}else{

?>

<tr>

<td colspan="7"
class="text-center">

Data penjualan tidak ditemukan.

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


<script>


/* =========================
   TAMBAH PRODUK
========================= */

function tambahProduk(){

    let tabel =
        document.getElementById("tabelProduk");

    let baris =
        document.querySelector(".barisProduk");

    let baru =
        baris.cloneNode(true);


    baru.querySelector(".produk").value = "";

    baru.querySelector(".harga").value = "Rp 0";

    baru.querySelector(".jumlah").value = 1;

    baru.querySelector(".subtotal").value = "Rp 0";


    tabel.appendChild(baru);

    nomorProduk();

}


/* =========================
   HAPUS PRODUK
========================= */

function hapusBaris(button){

    let semua =
        document.querySelectorAll(".barisProduk");


    if(semua.length > 1){

        button.closest("tr").remove();

        nomorProduk();

        hitungTotal();

    }

    else{

        let baris =
            button.closest("tr");


        baris.querySelector(".produk").value = "";

        baris.querySelector(".harga").value = "Rp 0";

        baris.querySelector(".jumlah").value = 1;

        baris.querySelector(".subtotal").value = "Rp 0";


        hitungTotal();

    }

}


/* =========================
   NOMOR PRODUK
========================= */

function nomorProduk(){

    let nomor =
        document.querySelectorAll(".nomor");


    nomor.forEach(function(item,index){

        item.innerHTML = index + 1;

    });

}


/* =========================
   HITUNG BARIS
========================= */

function hitungBaris(element){

    let baris =
        element.closest("tr");


    let produk =
        baris.querySelector(".produk");

    let jumlah =
        baris.querySelector(".jumlah");

    let hargaInput =
        baris.querySelector(".harga");

    let subtotalInput =
        baris.querySelector(".subtotal");


    let pilihan =
        produk.options[produk.selectedIndex];


    let harga =
        pilihan.getAttribute("data-harga");

    let stok =
        pilihan.getAttribute("data-stok");

    let qty =
        jumlah.value;


    if(harga){

        hargaInput.value =
            "Rp " +
            Number(harga).toLocaleString("id-ID");


        let subtotal =
            Number(harga) *
            Number(qty);


        subtotalInput.value =
            "Rp " +
            subtotal.toLocaleString("id-ID");

    }

    else{

        hargaInput.value = "Rp 0";

        subtotalInput.value = "Rp 0";

    }


    if(stok && Number(qty) > Number(stok)){

        jumlah.setCustomValidity(
            "Jumlah melebihi stok!"
        );

    }

    else{

        jumlah.setCustomValidity("");

    }


    hitungTotal();

}


/* =========================
   HITUNG TOTAL
========================= */

function hitungTotal(){

    let semua =
        document.querySelectorAll(".barisProduk");

    let total = 0;


    semua.forEach(function(baris){

        let produk =
            baris.querySelector(".produk");

        let jumlah =
            baris.querySelector(".jumlah");


        let pilihan =
            produk.options[produk.selectedIndex];


        let harga =
            pilihan.getAttribute("data-harga");


        if(harga && jumlah.value){

            total +=
                Number(harga) *
                Number(jumlah.value);

        }

    });


    document.getElementById("total").value =
        "Rp " +
        total.toLocaleString("id-ID");


    hitungKembalian();

}


/* =========================
   UBAH METODE PEMBAYARAN
========================= */

function ubahPembayaran(){

    let metode =
        document.getElementById("metode_pembayaran").value;

    let kolom =
        document.getElementById("kolomUangBayar");

    let uang =
        document.getElementById("uang_bayar");


    if(metode == "QRIS"){

        kolom.style.display = "none";

        uang.value = "";

        document.getElementById("kembalian").value =
            "Rp 0";

    }else{

        kolom.style.display = "block";

        hitungKembalian();

    }

}


/* =========================
   HITUNG KEMBALIAN
========================= */

function hitungKembalian(){

    let metode =
        document.getElementById("metode_pembayaran").value;


    let totalText =
        document.getElementById("total").value
        .replace("Rp","")
        .replace(/\./g,"")
        .trim();


    let total =
        Number(totalText) || 0;


    if(metode == "QRIS"){

        document.getElementById("kembalian").value =
            "Rp 0";

        return;

    }


    let bayar =
        Number(
            document.getElementById("uang_bayar").value
        ) || 0;


    if(bayar < total){

        let kurang =
            total - bayar;


        document.getElementById("kembalian").value =
            "Kurang Rp " +
            kurang.toLocaleString("id-ID");

    }else{

        let kembali =
            bayar - total;


        document.getElementById("kembalian").value =
            "Rp " +
            kembali.toLocaleString("id-ID");

    }

}


/* =========================
   SAAT HALAMAN DIBUKA
========================= */

ubahPembayaran();

</script>


</body>

</html>