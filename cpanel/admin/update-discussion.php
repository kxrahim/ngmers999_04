<?php
    require_once('includes/connection_old.php');

    $sql1 = "select *, a.id as aid
             from assessment_discussions as a
             join users as b on a.user_id = b.id";
    $rst1 = $conn->query($sql1);

    $b = 0;
    while ($row1 = $rst1->fetch_assoc()){
        $sql2 = "update assessment_discussions
                 set cohort_id = " . $row1['cohort_id'] . " 
                 where id = " . $row1['aid'];
        if ($conn->query($sql2) === TRUE) {
            //echo $sql2 . '<br>';
            ++$b;
        }       
    }

    //echo '<hr>Jumlah Data: ' . $b;


?>

<?php
    // update replies
    $sql1 = "select *, a.id as aid
             from assessment_discussion_replies as a
             join users as b on a.user_id = b.id";
    $rst1 = $conn->query($sql1);

    $b = 0;
    while ($row1 = $rst1->fetch_assoc()){
        $sql2 = "update assessment_discussion_replies
                 set cohort_id = " . $row1['cohort_id'] . " 
                 where id = " . $row1['aid'];
        if ($conn->query($sql2) === TRUE) {
            echo $sql2 . '<br>';
            ++$b;
        }       
    }

    echo '<hr>Jumlah Data: ' . $b;

?>