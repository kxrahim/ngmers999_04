<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semester Integration</title>

    <?php
        require_once('includes/connection.php');

        require_once ('includes/main-header.php');
    ?>
</head>
<body>
    <table class="table table-border">
        <thead>
            <tr>
                <th></th>
                <th>Student ID</th>
                <th>Cohort</th>
                <th>Nama</th>
                <th>Date</th>
                <th>Time</th>
                <th>Semester</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
                $sql = "SELECT a.id as aid, b.studentId, b.fullname, a.bookingDate, a.bookingTime, b.cohort_id, c.cohort, a.semester
                        FROM assessments AS a
                        JOIN users AS b ON a.user_id = b.id
                        JOIN cohorts AS c ON b.cohort_id = c.id
                        WHERE a.status = 1 and a.user_id = 4";
                $rst = $conn->query($sql);
                while ($row1 = $rst->fetch_assoc()){
                    $cohort = $row1['cohort'];

                    $cInt = $cohort[2];
                    $cTahun = substr($cohort, 3,4);
                    $cTahun2 = '20' . $cTahun;

                    // dapatkan tahun 
                    $gYear = date('Y', $row1['bookingDate']);                    

                    // dapatkan semester 
                    //$yNow = date('Y') - $cTahun2;
                    $yNow = $gYear - $cTahun2;
                    
                    $sem1 = $yNow * 2;

                    $cM = date('m');

                    if ($cM < 7){
                        $sem1 -= 1;
                    } 

                    // kemaskini semester
                    if ($row1['semester'] == 0){
                        $sql2 = "update assessments set semester = '$sem1' where id = '" . $row1['aid'] . "'";
                        $rst2 = $conn->query($sql2);
                    }


            ?>
            <tr>
                <td></td>
                <td><?=$row1['studentId']?></td>
                <td><?=$row1['cohort']?></td>
                <td><?=$row1['fullname']?></td>                
                <td><?=$row1['bookingDate']?></td>
                <td><?=$row1['bookingTime']?></td>
                <th>
                    <?php
                        //echo 'Nilai| ' . $cTahun . ' - ' . $cInt;
                        //echo 'Sem: ' . $sem1;
                        //echo '<br>Tahun | ' . $gYear;
                    ?>
                        <?=$row1['semester']?>
                </th>
            </tr>
            <?php
                }
            ?>
        </tbody>
    </table>
</body>
</html>