<?php
    require_once "includes/connection2.php";

    if (isset($_POST['query'])) {

        $query = "SELECT * 
                  FROM districts 
                  WHERE postcode LIKE '{$_POST['query']}%' 
                  GROUP BY postcode 
                  LIMIT 1";
        $result = mysqli_query($conn2, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($user = mysqli_fetch_array($result)) {
                //echo $user['fullname']."<br/>";
                $studentId = $user['postcode'];
                $fullname = $user['city'];

                echo json_encode(array('studentId'=>$studentId, 'fullname'=>$fullname));
            }
        } else {
            echo "<p style='color:#bfbbbb'>User not found...</p>";
        }

    }
?>