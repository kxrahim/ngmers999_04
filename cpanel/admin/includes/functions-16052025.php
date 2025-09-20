<!--
    - add reflection to student page
    - enhance reporting
-->

<?php

    function getDetail ($conn, $id) {
        $sql = "select *
                from users as a
                join salutations as b on a.salutation_id = b.id
                where a.id = '$id'";
        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        return array($row['salutation'], $row['fullname'], $row['email'], $row['cohort_id']);
    }

    function getUser($conn, $id){
        $sql = "select *
                from users as a
                join salutations as b on a.salutation_id = b.id
                where a.id = '$id'";
        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        echo $row['salutation'] . ' ' . $row['fullname'];
    }

    function getAssessor($conn, $id){
        $sql = "select *
                from users as a
                join salutations as b on a.salutation_id = b.id
                where a.id = '$id'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0){
            $row = $result->fetch_assoc();
            echo $row['salutation'] . ' ' . $row['fullname'];
        } else {
            echo 'Please enter Assessor name';
        }
        
    }

    function getRole($conn, $id){
        $sql = "select * from roles where id = '$id'";
        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        echo $row['name'];
    }

    function getSalutation($conn, $id){
        $sql = "select * from salutations where id = '$id'";
        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        echo strtoupper($row['salutation']);
    }

    function getCohort($conn, $id){

        if (!empty($id)){
            $sql = "select * from cohorts where id = '$id'";
            $result = $conn->query($sql);

            $row = $result->fetch_assoc();

            echo $row['cohort'];
        }
    }

    function getActivity($conn, $id){
        $sql = "select * from activities where id = '$id'";
        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        echo $row['activity'];
    }

    function getCase($conn, $id){
        $sql = "select * from cases where id = '$id'";
        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        echo $row['complexity'];
    }

    function getLevel($conn, $id){
        $sql = "select * from levels where id = '$id'";
        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        return array($row['sort'], $row['level']);
    }

    function getObservation($conn, $id){
        $sql = "select * from observations where id = '$id'";
        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        return $row['observation'];
    }

    function getObservationLevel($conn, $id){
        $sql = "select *
                from observations as a
                join levels as b on a.level_id = b.id
                where a.id = '$id'";
        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        echo 'Level ' . $row['levelId'] . ': ' . $row['level'];
    }


    function getSupervision($conn, $id){
        $sql = "select * from supervisions where id = '$id'";
        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        echo $row['supervision'];
    }

    /*
    function make_avatar($character){
        $path = '../../avatar' . time() . '.png';
        $image = imagecreate(200, 200);

        $red    = rand(0, 255);
        $green  = rand(0, 255);
        $blue   = rand(0, 255);

        // define background color
        imagecolorallocate($image, $red, $green, $blue);

        $textcolor = imagecolorallocate($image, 255, 255, 255);

        imagettftext($image, 100, 0, 55, 150, $textcolor, '../../font/ARIAL.ttf', $character);

        header('Content-Type: image/png');

        imagepng($image, $path);
        imagedestroy($image);

        return $path;

    }
    */

    function checkAvailability($conn, $id, $bookDate, $bookTime){
        //$id = 3;
        $bookDate = strtotime($bookDate);
        //$bookTime = '10:00';

        //echo 'Book Date: ' . $bookDate;
        $sql1 = "SELECT *
           FROM assessments
           WHERE bookingDate = '$bookDate' AND bookingTime = '$bookTime' AND id = '$id'";
        $result1 = $conn->query($sql1);

        if ($result1->num_rows > 0){
            $ada = 1;
        } else {
            $ada = 0;
        }

        return $ada;
    }

    function getOutcome ($conn, $id){
        $sql = "select * from outcomes where id = '$id'";
        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        //echo $row['supervision'];
        return array($row['outcome'], $row['description']);
    }

    function getGender ($id){
        if ($id == 'M'){
            echo 'Male';
        } else if ($id == 'F'){
            echo 'Female';
        } else {

        }
    }

    // get number of activities
    function getActivityTotal ($conn, $id){
        $num = 0;
        $sql = "select * from assessments
                where status = 1 and activity_id = '$id'";
        $result = $conn->query($sql);

        $num = $result->num_rows;

        return $num;
    }

    // get number of activities
    function getLecturerAssessmentTotal ($conn, $id){
        $num = 0;
        $sql = "select * from assessments
                where status = 1 and assessor_id = '$id'";
        $result = $conn->query($sql);

        $num = $result->num_rows;

        return $num;
    }

    function getTotalTopic($conn, $id){
        $num = 0;
        $sql = "select *
                from assessment_discussions
                where status = 1 and activity_id = '$id' and cohort_id";
        $result = $conn->query($sql);

        $num = $result->num_rows;

        return $num;
    }

    function getDiscussion($conn, $id){
        $sql = "select *
                from assessment_discussions
                where id = '$id'";
        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        return $row['discussion'];
    }

    function getTotalReply($conn, $id){
        $num = 0;
        $sql = "select *
                from assessment_discussion_replies
                where status = 1 and activity_id = '$id'";
        $result = $conn->query($sql);

        $num = $result->num_rows;

        return $num;
    }

    function getDiscussionReply($conn, $id){
        $num = 0;
        $sql = "select *
                from assessment_discussion_replies
                where status = 1 and assessment_discussion_id = '$id'";
        $result = $conn->query($sql);

        $num = $result->num_rows;

        return $num;
    }

    function getPatient($conn, $id){
        //$num = 0;
        $sql = "select *
                from patients
                where id = '$id'";
        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        echo $row['name'];
    }

    function getPatientId($conn, $mrn){
        //$num = 0;
        $sql = "select *
                from patients
                where  = '$mrn'";
        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        return $row['id'];
    }

    // get student assessment statistic
    function getTotalAssessment($conn, $id){
        $num = 0;
        $sql = "select *
                from assessments
                where user_id = '$id'";
        $result = $conn->query($sql);

        $num = $result->num_rows;
        //$row = $result->fetch_assoc();

        echo $num;
    }

    function getTotalCompleted($conn, $id){
        $num = 0;
        $sql = "select *
                from assessments
                where user_id = '$id' and status = 1";
        $result = $conn->query($sql);

        $num = $result->num_rows;
        //$row = $result->fetch_assoc();

        echo $num;
    }

    function getTotalWaiting($conn, $id){
        $num = 0;
        $sql = "select *
                    from assessments
                    where user_id = '$id' and status = 2 and (request_status = 1 or request_status = 2)";
        $result = $conn->query($sql);

        $num = $result->num_rows;
        //$row = $result->fetch_assoc();

        echo $num;
    }

    function getTotalReject($conn, $id){
        $num = 0;
        $sql = "select *
                        from assessments
                        where user_id = '$id' and request_status = 9";
        $result = $conn->query($sql);

        $num = $result->num_rows;
        //$row = $result->fetch_assoc();

        echo $num;
    }

    // dapatkan jumlah baris
    function getNilaiFeedback($id, $conn) {
        $num = 0;
        $sql = "select *
                from assessment_feedbacks
                where assessment_id = '$id'";
        $result = $conn->query($sql);

        $num = $result->num_rows;
        //$row = $result->fetch_assoc();
        return $num;
    }

    function getAssessmentCompleted($conn, $id, $aid){
        $num = 0;
        $sql = "select *
                from assessments
                where user_id = '$id' and reflection_id > 0 and activity_id = '$aid'";
        $result = $conn->query($sql);

        $num = $result->num_rows;
        //$row = $result->fetch_assoc();

        return $num;
    }

    function getAssessmentCompleted2($conn, $id, $aid){
        $num = 0;
        $sql = "select *
                from assessments
                where user_id = '$id' and activity_id = '$aid' and status = 1";
        $result = $conn->query($sql);

        $num = $result->num_rows;
        //$row = $result->fetch_assoc();

        return $num;
    }

    // -- individual completed activity 
    function getIndivdualCompletedActivity($conn, $id, $aid){
        $num = 0;
        $sql = "select *
                from assessments
                where user_id = '$id' 
                      and activity_id = '$aid'
                      and status = 1";
        $result = $conn->query($sql);

        $num = $result->num_rows;
        //$row = $result->fetch_assoc();

        return $num;
    }

    // -- get semester 
    function getSem ($conn, $id) {
        $sc1 = "select *
          from users as a
          join cohorts as b on a.cohort_id = b.id
          where a.id = " . $_SESSION['user_id'];
        $rct1 = $conn->query($sc1);

        $rcw1 = $rct1->fetch_assoc();

        if ($rcw1['role_id'] != 3){
            $cCohort = '00';
        } else {
            $cCohort = substr($rcw1['cohort'], 3,2);
        }

        //$cCohort = substr($rcw1['cohort'], 3,2);
        
        $cTahun = '20' . $cCohort;
        $yNow = date('Y') - $cTahun;

        if ($yNow == 0){
            $yNow = 1;
        }

        $sem = $yNow * 2;

        $cM = date('m');

        if ($cM < 7){
            $sem -= 1;
        }

        return $sem;
    }

    function getTimeLap($conn) {
        $sql = "select *
                from bookingTimes
                order by id desc";
        $rst = $conn->query($sql);

        $row = $rst->fetch_assoc();

        return $row['timelap'];
    }

    // get assessment duration 
    function getAssessmentDuration($conn, $id) {
        $sql = "SELECT *
                FROM assessment_assessors
                WHERE assessment_id = '$id'";
        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        return $row['duration'];
    }

    function getMinSession($conn, $actid) {
        $sql = "SELECT *
                FROM activities
                WHERE id = '$actid'";
        $result = $conn->query($sql);

        $row = $result->fetch_assoc();

        return $row['minReq'];
    }

?>
