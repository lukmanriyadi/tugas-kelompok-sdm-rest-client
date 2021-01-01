<?php
date_default_timezone_set("Asia/Jakarta");
require_once "./vendor/autoload.php";

use GuzzleHttp\Client;

session_start();

if (isset($_SESSION['email']) && isset($_SESSION['pass'])) {


    $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjVmY2NlODFlMThiNmVhMDAxNzdkNzVhMiIsImlhdCI6MTYwNzI2NDI4Nn0.DdOSTpw-Qjcm3p7lLEhSU3b0WkItmOZtZw_8o1COV8M";
    $client = new Client([
        // Base URI is used with relative requests
        'base_uri' => 'https://kelompok-sdm-rest-server.herokuapp.com/',
    ]);
    $clientFinance = new Client([
        // Base URI is used with relative requests
        'base_uri' => 'https://finance03app.herokuapp.com/',
    ]);
    $post_berhasil = 'Belum';
    $id_pegawai = "";
    $valid_id = false;
    if (isset($_GET['id'])) {
        $id_pegawai = $_GET['id'];
    }

    if (isset($_POST['search'])) {
        $id_pegawai = $_POST['id_pegawai'];
        $valid_id = true;
        try {
            $response = $client->request('GET', '/api/pegawai', [
                'query' => ['id' => $id_pegawai],
            ]);
            $result = json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            if ($statusCode == 404) {
                $valid_id = false;
            }
        }
    }

    if (isset($_POST['submit'])) {
        $id_pegawai_submit = $_POST['id_pegawai2'];
        $periode_bulan = $_POST['bulan'];
        $periode_tahun = $_POST['tahun'];
        $tanggal_pembayaran = date("Y-m-d H:i:s");
        $gaji_pokok = $_POST['gaji_pokok'];
        $gaji_total = $_POST['gaji_total'];
        $tunjangan = $_POST['tunjangan'];
        $potongan = 0;
        $deskripsi = $_POST['deskripsi'];
        $request_body = [
            'jenis' => 'credit',
            'biaya' => $gaji_total,
            'keterangan' => $deskripsi
        ];
        $headers = [
            'Authorization' => 'Bearer ' . $token,
        ];
        $r = $clientFinance->request('POST', '/transaksis', [
            'headers' => $headers,
            'form_params' => $request_body,
        ]);
        $res = json_decode($r->getBody()->getContents(), true);

        $id_transaksi = $res['id'];
        $status_transaksi = $res['status'];

        // var_dump($res);
        // echo $id_transaksi;
        // echo $status_transaksi;
        $request_body_post_penggajian = [
            'id_pembayaran' => $id_transaksi,
            'id_pegawai' => $id_pegawai_submit,
            'periode_bulan' => $periode_bulan,
            'periode_tahun' => $periode_tahun,
            'tgl_pembayaran' => $tanggal_pembayaran,
            'gaji_pokok' => $gaji_pokok,
            'gaji_total' => $gaji_total,
            'tunjangan' => $tunjangan,
            'potongan' => $potongan,
            'status' => $status_transaksi,
        ];
        //bikin post api penggajian (done)
        //import db terbaru (jangan dulu)
        //post data ke api kita
        $responsePostPenggajian = $client->request('POST', '/api/penggajian', [
            'form_params' => $request_body_post_penggajian,
        ]);

        $rPostPenggajian = json_decode($responsePostPenggajian->getBody()->getContents(), true);

        if ($rPostPenggajian['status'] == true) {
            $post_berhasil = 'Berhasil';
        } else {
            $post_berhasil = 'Gagal';
        }
    }
?>

    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>Data Department | Indomobil - Pusat data SDM</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.png">

        <!-- DataTables -->
        <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

        <!-- Responsive datatable examples -->
        <link href="assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="assets/css/app.css" rel="stylesheet" type="text/css" />

    </head>

    <body data-layout="horizontal" data-layout-size="boxed">

        <!-- Begin page -->
        <div id="layout-wrapper">

            <header id="page-topbar">
                <div class="navbar-header">
                    <div class="container-fluid">
                        <div class="float-right">

                            <div class="dropdown d-inline-block">
                                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img class="rounded-circle header-profile-user" src="assets/images/users/avatar-1.png" alt="Header Avatar">
                                    <span class="d-none d-sm-inline-block ml-1"><?php echo $_SESSION['nama_depan'] ?></span>
                                    <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <!-- item-->
                                    <a class="dropdown-item" href="logout.php"><i class="mdi mdi-logout font-size-16 align-middle mr-1"></i> Logout</a>
                                </div>
                            </div>
                        </div>

                        <!-- LOGO -->
                        <div class="navbar-brand-box">
                            <a href="index.html" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="assets/images/logo-sm-dark.png" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img style="padding-bottom:4px;" src="assets/images/logo-dark.png" alt="" height="28">
                                </span>
                            </a>

                            <a href="index.html" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="assets/images/logo-sm-light.png" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="assets/images/logo-light.png" alt="" height="20">
                                </span>
                            </a>
                        </div>

                        <button type="button" class="btn btn-sm mr-2 font-size-16 d-lg-none header-item waves-effect waves-light" data-toggle="collapse" data-target="#topnav-menu-content">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>

                        <div class="topnav">
                            <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

                                <div class="collapse navbar-collapse" id="topnav-menu-content">
                                    <ul class="navbar-nav">
                                        <li class="nav-item">
                                            <a class="nav-link" href="index.php">
                                                Dashboard
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" href="pegawai.php">
                                                Pegawai
                                            </a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="nav-link" href="department.php">
                                                Department
                                            </a>
                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="nav-link" href="penggajian.php">
                                                Penggajian
                                            </a>

                                        </li>

                                        <li class="nav-item dropdown">
                                            <a class="nav-link" href="absensi.php">
                                                Absensi
                                            </a>

                                        </li>

                                    </ul>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>


            </header>

            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">

                <div class="page-content">

                    <!-- Page-Title -->
                    <div class="page-title-box">
                        <div class="container-fluid">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="page-title mb-1">Tambah Data Penggajian</h4>
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item active"><a href="javascript: void(0);">Dashboard</a></li>
                                        <li class="breadcrumb-item active"><a href="javascript: void(0);">Data Penggjian</a></li>
                                        <li class="breadcrumb-item active">Tambah Data Penggajian</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end page title end breadcrumb -->

                    <div class="page-content-wrapper">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <?php
                                            if ($post_berhasil == "Berhasil") {
                                            ?>
                                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                    <strong>Input Berhasil</strong> Silahkan Cek di data Penggajian.
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                            <?php
                                            } else if ($post_berhasil == "Gagal") {
                                            ?>
                                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                                    <strong>Input Gagal!</strong> Pastikan mengisi data dengan benar.
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                            <?php
                                            }
                                            ?>

                                            <?php
                                            if ($valid_id) {
                                            ?>
                                                <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top:10px">
                                                    <strong>ID Sudah Valid</strong> Silahkan Lanjutkan Mengisi Kolom lain.
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                            <?php
                                            } else {
                                            ?>
                                                <div class="alert alert-warning alert-dismissible fade show" role="alert" style="margin-top:10px">
                                                    <strong>ID Belum Valid!</strong> Pastikan mengisi ID yg benar, dan lakukan cek ID.
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                            <form class="form-inline" method="POST" action="">
                                                <div class="form-group">
                                                    <label for="id_pegawai" class="sr-only">ID Pegawai</label>
                                                    <input type="text" class="form-control-lg" id="id_pegawai" name="id_pegawai" placeholder="ID Pegawai" value="<?= $id_pegawai; ?>" <?php if ($valid_id === true) {
                                                                                                                                                                                            echo "readonly";
                                                                                                                                                                                        } ?>>
                                                </div>
                                                <button name="search" type="submit" class="btn btn-primary btn-lg ml-5" <?php if ($valid_id === true) {
                                                                                                                            echo "disabled";
                                                                                                                        } ?>>Check ID Pegawai</button>
                                            </form>
                                            <!-- 
                                            start form gaji
                                        --> <?php
                                            if ($valid_id) {
                                            ?>
                                                <form method="POST" action="" style="margin-top:20px;">
                                                    <input type="hidden" name="id_pegawai2" value="<?= $id_pegawai ?>">
                                                    <div class="form-group">
                                                        <label for="nama_pegawai">Nama Pegawai</label>
                                                        <input type="text" class="form-control" id="nama_pegawai" placeholder="Nama Pegawai" value="<?= $result['data'][0]['nama_depan']; ?> <?= $result['data'][0]['nama_belakang']; ?>" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="divisi">Divisi</label>
                                                        <input type="text" class="form-control" id="divisi" placeholder="Divisi" value="<?= $result['data'][0]['nama_divisi']; ?>" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="jabatan">Jabatan</label>
                                                        <input type="text" class="form-control" id="jabatan" placeholder="jabatan" value="<?= $result['data'][0]['nama_jabatan']; ?>" readonly>
                                                    </div>
                                                    <div class="form-inline my-4">

                                                        <div class="form-group">
                                                            <label for="bulan" class="mr-2">Bulan</label>
                                                            <select class="form-control" id="bulan" name="bulan" onchange="updateDeskripsi()">
                                                                <option value="Januari">Januari</option>
                                                                <option value="Februari">Februari</option>
                                                                <option value="Maret">Maret</option>
                                                                <option value="April">April</option>
                                                                <option value="Mei">Mei</option>
                                                                <option value="Juni">Juni</option>
                                                                <option value="Juli">Juli</option>
                                                                <option value="Agustus">Agustus</option>
                                                                <option value="September">September</option>
                                                                <option value="Oktober">Oktober</option>
                                                                <option value="November">November</option>
                                                                <option value="Desember">Desember</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group mx-5">
                                                            <Tabel for="tahun" class="mr-2">Tahun</Tabel>
                                                            <select class="form-control" id="tahun" name="tahun" onchange="updateDeskripsi()">
                                                                <option value="2020">2020</option>
                                                                <option value="2019">2019</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="Gaji Pokok">Gaji Pokok</label>
                                                        <input type="number" min="0" class="form-control" id="gaji_pokok" placeholder="Gaji Pokok" name="gaji_pokok" onkeyup="hitungTotal()">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="Tunjangan">Tunjangan</label>
                                                        <input type="number" min="0" class="form-control" id="tunjangan" name="tunjangan" placeholder="Tunjangan" onkeyup="hitungTotal()">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="Total">Total</label>
                                                        <input type="number" min="0" class="form-control" id="gaji_total" name="gaji_total" placeholder="Total" readonly>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="Deskripsi">Deskripsi</label>
                                                        <textarea name="deskripsi" class="form-control" id="deskripsi" rows="3" readonly></textarea>
                                                    </div>
                                                    <button name="submit" type="submit" class="btn btn-primary">Submit</button>
                                                </form>

                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div> <!-- end col -->
                            </div> <!-- end row -->

                        </div>
                        <!-- end container-fluid -->
                    </div>
                    <!-- end page-content-wrapper -->
                </div>
                <!-- End Page-content -->


            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->
        <script>
            function updateDeskripsi() {
                let nama_pegawai = document.getElementById('nama_pegawai').value;
                let periode_bulan = document.getElementById('bulan').value;
                let periode_tahun = document.getElementById('tahun').value;
                let deskripsi = `Penggajian ${nama_pegawai} Periode ${periode_bulan} Tahun ${periode_tahun}`;
                document.getElementById("deskripsi").value = deskripsi;
            }

            function hitungTotal() {
                let gaji_pokok = parseInt(document.getElementById('gaji_pokok').value);
                let tunjangan = parseInt(document.getElementById('tunjangan').value);

                let total = gaji_pokok + tunjangan;
                document.getElementById("gaji_total").value = total;
            }
        </script>
        <!-- JAVASCRIPT -->
        <script src="assets/libs/jquery/jquery.min.js"></script>
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>

        <script src="https://unicons.iconscout.com/release/v2.0.1/script/monochrome/bundle.js"></script>

        <!-- Required datatable js -->
        <script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
        <!-- Buttons examples -->
        <script src="assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
        <script src="assets/libs/jszip/jszip.min.js"></script>
        <script src="assets/libs/pdfmake/build/pdfmake.min.js"></script>
        <script src="assets/libs/pdfmake/build/vfs_fonts.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
        <!-- Responsive examples -->
        <script src="assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

        <!-- Datatable init js -->
        <script src="assets/js/pages/datatables.init.js"></script>

        <script src="assets/js/app.js"></script>

    </body>

    </html>

<?php
} else {
    header("Location: login_form.php");
    exit();
}

?>