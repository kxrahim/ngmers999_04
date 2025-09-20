<?php
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
  ini_set('session.save_path', '/var/lib/php/sessions');
?>

<?php

    session_start();
	include("admin/includes/connection.php"); //Establishing connection with our database

	$error = ""; //Variable for storing our errors.

    $url = $_SERVER['REQUEST_URI'];

    // Extract the query part of the URL
    $encrypted = parse_url($url, PHP_URL_QUERY);

    // echo $queryString;

    // $encrypted = $_GET['t'];
    $key = "ngmers-999"; // Keep this safe!

    // DECRYPT (unlock)
    $decrypted = openssl_decrypt(base64_decode($encrypted), 'AES-128-ECB', $key);
    //echo '<br>' . $decrypted;
    // 99&i=2&u=azrin@knowix.my&u=azrin&f=Administrator
    $parts = explode('&', $decrypted);
    
    $params = explode('&', $decrypted);
    $result = [];

    foreach ($params as $param) {
        $parts = explode('=', $param, 2); // Split into max 2 parts
        $key = $parts[0] ?? '';
        $value = $parts[1] ?? '';
        
        // Store only non-empty keys and override duplicates
        if (!empty($key)) {
            $result[$key] = $value;
        }
    }

    // Get values
    $t = $result['t'] ?? null; // 'azrin' (last occurrence)
    $i = $result['i'] ?? null; // 'azrin' (last occurrence)
    $u = $result['u'] ?? null; // 'azrin' (last occurrence)
    $f = $result['f'] ?? null; // 'Administrator'

    //exit;

    if ($t == '99'){
        //echo 'Flow 1 Pass......<br>';
		
		// Define $username and $password
		$uid		= $i;
        $username	= $u;
        $email		= $e;

        //print_r($_GET);

		$sql = "SELECT
					u.firstname as ufirstname,
					u.lastname as ulastname,
					u.email as uemail,
					ra.userid,
					r.shortname AS role_shortname,
					r.name AS role_fullname,
                    ra.contextid
				FROM
					elp_role_assignments ra
				JOIN
					elp_role r ON r.id = ra.roleid
				JOIN
					elp_context c ON c.id = ra.contextid
				JOIN 
					elp_user u ON u.id = ra.userid
				WHERE
					ra.userid = '$uid'";
        //AND c.contextlevel = 50

		$result = $conn->query($sql);
		$row = $result->fetch_assoc();

        //print_r($row);
        //exit;

        if ($result->num_rows > 0){
            //echo 'Flow 2 Pass......<br>';

            $_SESSION['user_id']  = $uid;
            $_SESSION['firstname'] = $row['ufirstname'];
            $_SESSION['lastname'] = $row['ulastname'];
            $_SESSION['role']     = $row['role_shortname'];
            $_SESSION['email']    = $row['uemail']; 
            
            if (($_SESSION['role'] == 'trainingteam' ) || ($_SESSION['role'] == 'staff' ) || ($_SESSION['role'] == 'motrc' ) || ($_SESSION['role'] == 'motrcteam' )) {
                header("location: admin/index.php"); // Redirecting To Other Page
            } else {

            }

            
        } else {
            //echo 'Tiada data';
        }
    } else {
        //echo 'Masuk sini......' . '<br>';
    }

    //echo 'tiada';
	
?>