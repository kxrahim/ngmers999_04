<?php
    function getUserInfoField ($conn, $field) {
        
    }

    function getUserInfoDataAgency ($conn, $field) {
        $sql ="select *
               from elp_user_info_data 
               where fieldid = 18 and userid = " . $field;
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
             $row = $result->fetch_assoc();
            return $row['data'];
        } else {
            return '-';
        }
    }

    function getUserInfoDataLocation ($conn, $field) {
        $sql ="select *
               from elp_user_info_data 
               where fieldid = 21 and userid = " . $field;
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
             $row = $result->fetch_assoc();
            return $row['data'];
        } else {
            return '-';
        }
       
    }

    function getCheckStaff ($conn, $id) {
        $sql = "SELECT *
                FROM elp_user_info_data
                WHERE userid = '$id'
                    AND (fieldid = 2 AND data != '')";
        $result = $conn->query($sql);

        if ($result->num_rows > 0){
            return 1;
        } else {
            return 0;
        }
    }

    function getItemMark ($conn, $item, $complete) {
        $sql = "SELECT *
                FROM elp_feedback_value
                WHERE item = " . $item . " AND completed = " . $complete;
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

        return $row['value'];
    }
?>