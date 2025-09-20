<?php
    require_once "includes/connection.php";

    if (isset($_POST['query'])) {

        $query = "SELECT * FROM users WHERE fullname LIKE '{$_POST['query']}%' LIMIT 100";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($user = mysqli_fetch_array($result)) {
                //echo $user['fullname']."<br/>";
                $studentId = $user['studentId'];
                $fullname = $user['fullname'];

                echo json_encode(array('studentId'=>$studentId, 'fullname'=>$fullname));
            }
        } else {
            echo "<p style='color:#bfbbbb'>User not found...</p>";
        }

    }
?>