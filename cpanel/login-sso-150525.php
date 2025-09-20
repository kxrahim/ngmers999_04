<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
?>

<?php
	session_start();
	include("admin/includes/connection.php"); //Establishing connection with our database

	$error = ""; //Variable for storing our errors.
	
	if ($_GET['t'] == '99'){
		
		// Define $username and $password
		$uid		= $_GET['i'];
        $username	= $_GET['u'];
        $email		= $_GET['e'];

		// $sql = "SELECT
		// 			ra.userid,
		// 			r.shortname AS role_shortname,
		// 			r.name AS role_fullname,
		// 			c.contextlevel,
		// 			c.instanceid
		// 		FROM
		// 			elp_role_assignments ra
		// 		JOIN
		// 			elp_role r ON r.id = ra.roleid
		// 		JOIN
		// 			elp_context c ON c.id = ra.contextid
		// 		WHERE
		// 			ra.userid = '$uid'";

		$sql = "SELECT
					u.firstname as ufirstname,
					u.lastname as ulastname,
					u.email as uemail,
					ra.userid,
					r.shortname AS role_shortname,
					r.name AS role_fullname
				FROM
					elp_role_assignments ra
				JOIN
					elp_role r ON r.id = ra.roleid
				JOIN
					elp_context c ON c.id = ra.contextid
				JOIN 
					elp_user u ON u.id = ra.userid
				WHERE
					ra.userid = '$uid'
					AND c.contextlevel = 50";

		$result = $conn->query($sql);
		$row = $result->fetch_assoc();

		print_r($row);

		exit;

        //To protect from MySQL injection
        //$username = stripslashes($username);
        //$username = mysqli_real_escape_string($conn, $username);

		//echo 'Username: ' . $username;

		//print_r($_GET);
		//exit;
		
		//Check username and password from database
        //$sql = "SELECT * FROM users WHERE username = '$username' AND email = '$email'";
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($sql);
        $row = mysqli_fetch_array($result,MYSQLI_ASSOC);

        //echo 'Username: ' . $username;
        //echo $sql;
        
        //exit;
        if(mysqli_num_rows($result) > 0){

            $_SESSION['user_id']  = $row['id'];
            $_SESSION['username'] = $row['username']; // Initializing Session
            $_SESSION['fullname'] = $row['fullname'];
            $_SESSION['role']     = $row['role_id'];
            $_SESSION['email']    = $row['email'];            
            $_SESSION['program_id']     = $row['program_id'];
            $_SESSION['picStatus']      = $row['picStatus'];
            $_SESSION['picture']        = $row['picture'];
            $_SESSION['nokp']           = $row['nokp'];
            $_SESSION['mobile']         = $row['mobile'];

            

            if (($_SESSION['role'] == 1) || ($_SESSION['role'] == 2)) {
                // save action to logs
                $sql = $conn->query("INSERT INTO logs(user_id, activity) VALUES ('" . $_SESSION['user_id'] . "', 'login')");
                header("location: admin/index.php"); // Redirecting To Other Page
            } else if ($_SESSION['role'] == 3){
                // save action to logs
                $sql = $conn->query("INSERT INTO logs(user_id, activity) VALUES ('" . $_SESSION['user_id'] . "', 'login')");
                header("location: lecturer/index.php"); // Redirecting To Other Page
            } else if ($_SESSION['role'] == 4){
                header("location: facilitator/index.php"); // Redirecting To Other Page
            } 

            else if ($_SESSION['role'] == 6){
                header("location: marketing/index.php"); // Redirecting To Other Page
            }
            else if ($_SESSION['role'] == 5){
                header("location: hod/index.php"); // Redirecting To Other Page
            } else {
            	/*
				echo "<script type='text/javascript'>alert('Sorry! Academic Management System currently not accessible. Please come later.');
                    window.location='https://ticdigital.tic.edu.my/my/';
                    </script>";
				exit();
            	*/

                if (($_SESSION['program_id'] == 0) || ($_SESSION['picStatus'] == 0) || is_null($_SESSION['nokp']) || is_null($_SESSION['mobile'])){
                    header("location: user/profile-update.php");
                    exit;
                } else {
                    $sql = $conn->query("INSERT INTO logs(user_id, activity) VALUES ('" . $_SESSION['user_id'] . "', 'login')");
                    header("location: user/index.php");
                    exit;
                }
				
                //header("location: user/index.php"); // Redirecting To Other Page
            }

        }else
        {
            //$error = "Incorrect username or password.";
            //header("location: index-error.php"); // Redirecting To Other Page
            echo "<script type='text/javascript'>alert('Sorry! Invalid username or password. Please re-login again. ');
                    window.location='index.php';
                  </script>";
            exit();
        }
 		
	}
?>