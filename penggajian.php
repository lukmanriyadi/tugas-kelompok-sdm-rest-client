<?php
require_once "./vendor/autoload.php";

use GuzzleHttp\Client;

$client = new Client([
    // Base URI is used with relative requests
    'base_uri' => 'https://kelompok-sdm-rest-server.herokuapp.com/',
]);
$clientFinance = new Client([
    // Base URI is used with relative requests
    'base_uri' => 'https://finance03app.herokuapp.com/',
]);
$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjVmY2NlODFlMThiNmVhMDAxNzdkNzVhMiIsImlhdCI6MTYwNzI2NDI4Nn0.DdOSTpw-Qjcm3p7lLEhSU3b0WkItmOZtZw_8o1COV8M";
//ambil data penggajian
$response = $client->request('GET', '/api/penggajian', []);

//ubah respon json jadi array di php.
$result = json_decode($response->getBody()->getContents(), true);

session_start();

$goingCheck = false;
$isStatusChange = false;
if (isset($_POST['lookup'])) {
    $goingCheck = true;
    $id_transaksi = $_POST['id_transaksi'];
    $status_transaksi = $_POST['status'];
    $headers = [
        'Authorization' => 'Bearer ' . $token,
    ];

    $target = '/status/' . $id_transaksi;

    $responseLookup = $clientFinance->request('GET', $target, [
        'headers' => $headers
    ]);
    //ubah respon json jadi array di php.
    $resultLookup = json_decode($responseLookup->getBody()->getContents(), true);

    if ($status_transaksi != $resultLookup['status']) {
        $isStatusChange = true;
    }
}

if (isset($_SESSION['email']) && isset($_SESSION['pass'])) {

?>

    <!doctype html>
    <html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>Data Penggajian | Indomobil - Pusat data SDM</title>
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
                                    <span class="d-none d-sm-inline-block ml-1"><?php echo $_SESSION['name'] ?></span>
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
                                    <h4 class="page-title mb-1">Data Penggajian</h4>
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Data Penggajian</li>
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
                                            if ($goingCheck == true) {
                                                if ($isStatusChange == true) {
                                            ?>
                                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                        Transaksi Dengan ID <strong><?= $id_transaksi; ?></strong> Statusnya Telah diubah di sisi Finance.
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                <?php
                                                } else {
                                                ?>
                                                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                                        Transaksi Dengan ID <strong><?= $id_transaksi; ?></strong> Statusnya masih sama dengan sisi Finance.
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            <?php
                                            }
                                            ?>
                                            <a href="tambah_penggajian.php" class="btn btn-success" style="margin-bottom:10px;">Tambah Data</a>
                                            <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                                <thead>
                                                    <tr>
                                                        <th class="col-head">ID Pembayaran</th>
                                                        <th class="col-head">Nama Pegawai</th>
                                                        <th class="col-head">Periode</th>
                                                        <th class="col-head">Tanggal Pembayaran</th>
                                                        <th class="col-head">Gaji Pokok</th>
                                                        <th class="col-head">Gaji Total</th>
                                                        <th class="col-head">Tunjangan</th>
                                                        <th class="col-head">Potongan</th>
                                                        <th class="col-head">Status</th>
                                                        <th class="col-head">Lookup</th>

                                                    </tr>
                                                </thead>

                                                <tbody>

                                                    <?php
                                                    foreach ($result['data'] as $a) {
                                                    ?>
                                                        <tr>
                                                            <th class="bold-text">
                                                                <?php echo $a['id_pembayaran']; ?>
                                                            </th>
                                                            <th class="bold-text"> <?php echo $a['nama_depan']; ?> <?php echo $a['nama_belakang']; ?></th>
                                                            <td> <?php echo $a['periode']; ?> </td>
                                                            <td> <?php echo $a['tgl_pembayaran']; ?> </td>
                                                            <td> <?php echo $a['gaji_pokok']; ?> </td>
                                                            <td> <?php echo $a['gaji_total']; ?> </td>
                                                            <td> <?php echo $a['tunjangan']; ?> </td>
                                                            <td> <?php echo $a['potongan']; ?> </td>
                                                            <td> <?php echo $a['status']; ?> </td>
                                                            <td>
                                                                <form action="" method="POST">
                                                                    <input type="hidden" name="id_transaksi" value="<?php echo $a['id_pembayaran']; ?>" />
                                                                    <input type="hidden" name="status" value="<?php echo $a['status']; ?>" />
                                                                    <button name="lookup" type="submit" class="btn btn-primary btn-sm">LookUp</button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>

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