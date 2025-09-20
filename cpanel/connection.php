<?php

    // connect to database
    $dbname		= 'elp_mers999';
    $host		= '10.245.141.240';
    $dbuser		= 'mers999Admin';
    $dbpass		= 'Mer$999@dm!n';

    // create connection
    $conn = new mysqli($host, $dbuser, $dbpass, $dbname);

    // check connection
    if ($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    } else {
        echo 'connection ok';
    }

?>
