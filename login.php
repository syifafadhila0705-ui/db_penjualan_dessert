<?php

session_start();

include "koneksi.php";


/* JIKA SUDAH LOGIN */

if(isset($_SESSION['login'])){

    if($_SESSION['level'] == "admin"){

        header("location:admin/index.php");
        exit;

    }elseif($_SESSION['level'] == "kasir"){

        header("location:kasir/index.php");
        exit;

    }

}


/* PROSES LOGIN */

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $query = mysqli_query($koneksi, "
        SELECT * FROM user
        WHERE username='$username'
        AND password='$password'
    ");


    if(mysqli_num_rows($query) > 0){

        $data = mysqli_fetch_assoc($query);

        $_SESSION['login'] = true;
        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['level'] = $data['level'];


        /* ADMIN */

        if($data['level'] == "admin"){

            header("location:admin/index.php");
            exit;

        }


        /* KASIR */

        elseif($data['level'] == "kasir"){

            header("location:kasir/index.php");
            exit;

        }

    }else{

        $pesan = "Username atau password salah";

    }

}

?>

<!DOCTYPE html>
<html>
<head>

    <title>Login - Sweet Dessert</title>

    <link rel="stylesheet"
    href="assets/bootstrap-5.3.8-dist/bootstrap-5.3.8-dist/css/bootstrap.min.css">

</head>

<body style="background-color:#ffd6e7;">

<div class="container">

    <div class="row justify-content-center align-items-center min-vh-100">

        <div class="col-md-6 col-lg-5">

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">


                <!-- HEADER PINK -->

                <div
                    class="text-center p-4"
                    style="background-color:#f8a8c4;">

                    <h1 class="fw-bold text-white mb-1">
                        Sweet Dessert
                    </h1>

                    <p class="text-white mb-0">
                        Sistem Penjualan Dessert
                    </p>

                </div>


                <!-- FORM -->

                <div class="card-body p-5">

                    <div class="text-center mb-4">

                        <h4 class="fw-bold mb-2">
                            Selamat Datang
                        </h4>

                        <p class="text-secondary mb-0">
                            Silakan login untuk masuk ke sistem
                        </p>

                    </div>


                    <!-- PESAN ERROR -->

                    <?php if(isset($pesan)){ ?>

                        <div class="alert alert-danger text-center">

                            <?php echo $pesan; ?>

                        </div>

                    <?php } ?>


                    <form method="post">


                        <!-- USERNAME -->

                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Username
                            </label>

                            <input
                                type="text"
                                name="username"
                                class="form-control form-control-lg"
                                placeholder="Masukkan username"
                                required>

                        </div>


                        <!-- PASSWORD -->

                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control form-control-lg"
                                placeholder="Masukkan password"
                                required>

                        </div>


                        <!-- LOGIN -->

                        <button
                            type="submit"
                            name="login"
                            class="btn btn-lg w-100 text-white"
                            style="background-color:#d63384;">

                            Login

                        </button>


                    </form>


                    <div class="text-center mt-4">

                        <small class="text-secondary">
                            Admin dan Kasir
                        </small>

                    </div>


                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>