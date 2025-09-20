<?php
    //include("../check.php");
    session_start();

    if (isset($_SESSION['user_id'])){

    } else {
        header('Location: ../login-sso.php');
    }
    $urole = $_SESSION['role'];
    $uid 	= $_SESSION['user_id'];

    $nyear = date('Y');
    $nmonth = date('m');

    $cdate = strtotime(date('Y-m-d', time()). '00:00:00');
    
?>
<?php
session_start();

// Check if user is logged in by verifying both session and cookie
if (!isset($_SESSION['user_id']) || !isset($_COOKIE['auth_token'])) {
    //header("Location: login.php");
    header("Location: https://academy.999.gov.my");
    exit();
}

// Session timeout (e.g., 30 minutes)
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: https://academy.999.gov.my");
    exit();
}
$nyear = date('Y');
$nmonth = date('m');

$cdate = strtotime(date('Y-m-d', time()). '00:00:00');
$_SESSION['LAST_ACTIVITY'] = time();
?>