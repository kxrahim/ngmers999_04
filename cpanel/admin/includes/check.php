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