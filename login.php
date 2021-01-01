<?php
session_start();

$host = "freedb.tech";
$user = "freedbtech_kelompoksdmadmin";
$password = "kelompoksdm";
$db = "freedbtech_kelompoksdm";

$konek = mysqli_connect($host, $user, $password, $db);

if (!$konek) {
    echo "Connection failed";
}
if (isset($_POST['email']) && isset($_POST['password'])) {
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        return $data;
    }
    $email = validate($_POST['email']);
    $pass = validate($_POST['password']);

    if (empty($email)) {
        header("Location: login-form.php?error=User Name is required");
        exit();
    } else if (empty($pass)) {
        header("Location: login-form.php?error=Password is required");
        exit();
    } else {
        $sql = "SELECT * FROM login WHERE email='$email' AND pass='$pass'";

        $result = mysqli_query($konek, $sql);

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if ($row['email'] === $email && $row['pass'] === $pass) {
                $_SESSION['nama_depan'] = $row['nama_depan'];
                $_SESSION['nama_belakang'] = $row['nama_belakang'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['pass'] = $row['pass'];
                header("Location: index.php");
                exit();
            } else {
                header("Location: login-form.php?error=Please make sure your email and password are correct with your account.");
                exit();
            }
        } else {
            header("Location: login-form.php?error=Please make sure your email and password are correct with your account.");
            exit();
        }
    }
} else {
    header("Location: login-form.php");
    exit();
}
