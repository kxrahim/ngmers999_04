<?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('session.save_path', '/var/lib/php/sessions');
?>
<?php
    include("includes/check.php");
?>
<!doctype html>
<!--
* Bootstrap Simple Admin Template
* Version: 2.1
* Author: Alexis Luna
* Website: https://github.com/alexis-luna/bootstrap-simple-admin-template
-->
<html lang="en">

<head>
    <?php
        require_once('includes/main-header.php');
        require_once('includes/connection_old.php');
        require_once('includes/functions.php');

        if (isset($_GET['n'])){
            $n = $_GET['n'];
        } else {
            $n = 0;
        }

        // for filtering
        $conditions = '';
        if (isset($_GET['filter'])){
            //print_r($_GET);
            //exit;


//            if (!empty($_GET['date_from']))
//            {
//                $dateFrom = strtotime($_GET['date_from']);
//                if (!empty($_GET['date_to']))
//                {
//                    $dateTo = strtotime($_GET['date_to']);
//                    $conditions .= ' and (bookingDate >= ' . $dateFrom . " and a.bookingDate <= " . $dateTo . ")" ;
//                }
//                else
//                {
//                    $conditions .= ' and a.bookingDate >= ' . $dateFrom;
//                }
//
//            }

//            if ($_GET['activity_id'] > 0){
//                $conditions .= ' and a.activity_id = ' . $_GET['activity_id'];
//            }

            if ($_GET['assessor_id'] > 0){
                $conditions .= ' and a.id = ' . $_GET['assessor_id'];
            }

        }

    ?>

    <style>
        .accordion-button {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
            padding: 1rem 1.25rem;
            font-size: 1rem;
            color: #212529;
            text-align: left;
            background-color: #c1ebf7;
            border: 0;
            border-radius: 0;
            overflow-anchor: none;
            transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out, border-radius .15s ease;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- sidebar -->
        <?php
            require_once('includes/admin-sidebar.php');
        ?>
        <div id="body" class="active">
            <!-- navbar navigation component -->
            <?php
                require_once('includes/navbar.php');
            ?>
            <!-- end of navbar navigation -->
            <div class="content">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 page-header">
                            <div class="page-pretitle">Report</div>
                            <h2 class="page-title">Student Details</h2>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">Student Information</div>
                                <div class="card-body">
                                    <?php
                                        $sql01 = "select *
                                                  from users
                                                  where id = " . $_GET['s'];
                                        $rst01 = $conn->query($sql01);
                                        $rw1 = $rst01->fetch_assoc();
                                    ?>
                                    <table class="table table-responsive">
                                        <tr>
                                            <td>Name</td>
                                            <th><?=$rw1['fullname'];?></th>
                                        </tr>
                                        <tr>
                                            <td>Email</td>
                                            <th><?=$rw1['email'];?></th>
                                        </tr>
                                        <tr>
                                            <td>Cohort</td>
                                            <th><?=getCohort($conn, $rw1['cohort_id']);?></th>
                                        </tr>
                                    </table>

                                    <h6><b>List of Assessments</b></h6>
                                    <table class="table table-hover table-striped" id="dataTables-examplex" width="100%">
                                        <thead>
                                            <tr>
                                                <th>ACCEPTED</th>
                                                <th>COMPLETED</th>
                                                <th>REJECTED</th>
                                                <!-- <th>PENDING</th> -->
                                                <th>TOTAL</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $sq1 = "SELECT *
                                                        from users as a
                                                        WHERE id = " . $_GET['s'];
                                                $rs1 = $conn->query($sq1);

                                                //echo $sq1;

                                                $num = 0;

                                                while ($rw1 = $rs1->fetch_assoc()){
                                                    // detail
                                                    $assId = $rw1['id'];

                                                    $sn2 = "SELECT *
                                                            FROM assessments
                                                            WHERE user_id = '$assId'";
                                                    $rsn2 = $conn->query($sn2);

                                                    $sAccept = 0;
                                                    $sReject = 0;
                                                    $sPending = 0;
                                                    $sCompleted = 0;
                                                    while ($rws2 = $rsn2->fetch_assoc()){
                                                        if ($rws2['request_status'] == 1){
                                                            $sAccept = $sAccept + 1;
                                                        } else if ($rws2['request_status'] == 2){
                                                            $sReject = $sReject + 1;
                                                        } else if ($rws2['request_status'] == 9){
                                                            $sPending = $sPending + 1;
                                                        } else {

                                                        }

                                                        if ($rws2['reflection_id'] != 0){
                                                            $sCompleted += 1;
                                                        }

                                                    }
                                            ?>
                                            <tr>

                                                <td><?=$sAccept;?></td>
                                                <td><?=$sCompleted;?></td>
                                                <td><?=$sReject;?></td>
                                                <!-- <td><?=$sPending;?></td> -->
                                                <!--                                                <td>-->
                                                <?//=getLecturerAssessmentTotal ($conn, $rw1['id']);?>
                                                <!--</td>-->
                                                <td><?=$sAccept+$sReject;?></td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!--
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">Detail of activities</div>
                                <div class="card-body">
                                    <p class="card-title"></p>
                                    <table class="table table-hover" id="dataTables-example" width="100%">
                                        <thead>
                                            <tr>
                                                <th width="100px">DATE</th>
                                                <th>ASSESSOR</th>
                                                <th>ACTIVITY</th>                                 <th>DURATION (minutes)</th>
                                                <th>OUTCOME</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $sq1 = "SELECT *, a.id as aid
                                                        from assessments as a
                                                        join activities as b on a.activity_id = b.id
                                                        join observations as c on a.observation_id = c.id
                                                        join assessment_assessors as d on a.id = d.assessment_id
                                                        where a.user_id = " . $_GET['s'] . " and a.reflection_id > 0
                                                        order by bookingDate asc";
                                                $rs1 = $conn->query($sq1);

                                               //echo $sq1;

                                                $num = 0;
                                                while ($rw1 = $rs1->fetch_assoc()){

                                            ?>
                                            <tr>
                                                <th>
                                                    <?=date('d-m-Y', $rw1['bookingDate']);?>
                                                    <br>
                                                    <?=$rw1['bookingTime'];?>
                                                </th>
                                                <th><?=getUser($conn, $rw1['assessor_id']);?></th>
                                                <td><?=$rw1['activity'];?></td>
                                                <td><?=$rw1['duration'];?></td>
                                                <td><?=$rw1['outcome'];?></td>
                                                <th><a href="?a=<?=$rw1['aid'];?>">Details</a></th>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        -->
                        <!--
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">Detail of activities</div>
                                <div class="card-body">
                                    <p class="card-title"></p>
                                    <table class="table table-hover table-bordered" id="dataTables-example" width="100%">
                                        <thead>
                                            <tr>
                                                <th>ACTIVITY</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $sq3 = "select *
                                                        from activities
                                                        order by id asc";
                                                $rs3 = $conn->query($sq3);
                                                while ($rw3 = $rs3->fetch_assoc()){
                                                    $aid = $rw3['id'];
                                                    $sq4 = "select *
                                                            from observations
                                                            where activity_id = '$aid'";
                                                    $rs4 = $conn->query($sq4);
                                                    $nrow = $rs4->num_rows;
                                            ?>
                                            <tr rowspan="<?=$nrow;?>">
                                                <td><?=$rw3['activity'];?></td>
                                            </tr>
                                            <?php
                                                    while ($rw4 = $rs4->fetch_assoc()){
                                            ?>
                                                <tr>
                                                    <td><?=$rw4['observation'];?></td>
                                                </tr>
                                            <?php
                                                    }
                                                }
                                            ?>
                                            <tr>

                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        -->

                        <!-- accordian activity -->
                        <!--
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Accordion Item #1
                                </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                                </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Accordion Item #2
                                </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                                </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Accordion Item #3
                                </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                                </div>
                                </div>
                            </div>
                        </div>
                        -->
                        <h4>Assessment activities</h4>
                        <div class="accordion" id="accordionExample">
                            <?php
                                $sq3 = "select *
                                        from activities
                                        order by id asc";
                                $rs3 = $conn->query($sq3);

                                $a=0;
                                while ($rw3 = $rs3->fetch_assoc()){
                                    $a += 1;
                                    $aid = $rw3['id'];

                                    $turun = '';
                                    if ($a == 1){
                                        $turun = ' show';
                                    } else {
                                        $turun = '';
                                    }

                            ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?=$a;?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$a;?>" aria-expanded="true1" aria-controls="collapse<?=$a;?>">
                                    <?=$rw3['activity'];?>
                                </button>
                                </h2>
                                <div id="collapse<?=$a;?>" class="accordion-collapse collapse <?=$turun;?>" aria-labelledby="heading<?=$a;?>" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <table class="table table-bordered table-responsive">
                                        <tr>
                                            <th>Observation</th>
                                            <th>Date Completed</th>
                                            <th>Assessor</th>
                                        </tr>
                                        <?php
                                            $sq4 = "select *
                                                    from observations
                                                    where activity_id = '$aid'";
                                            $rs4 = $conn->query($sq4);
                                            $nrow = $rs4->num_rows;
                                            while ($rw4 = $rs4->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td><?=$rw4['observation'];?></td>
                                            <!-- Get assessment activity -->
                                            <?php
                                                $sc1 = "select *
                                                        from assessments as a
                                                        join assessment_assessors as b on a.id = assessment_id
                                                        where a.user_id = " . $_GET['s']. "
                                                            and a.activity_id = '$aid'
                                                            and a.observation_id = " . $rw4['id'] . "
                                                            and a.reflection_id > 0";

                                                //echo $sc1;
                                                $rsc1 = $conn->query($sc1);
                                            ?>
                                            <td>
                                            <?php
                                                if ($rsc1->num_rows > 0){
                                                    while ($rwc1 = $rsc1->fetch_assoc()){
                                                        echo date('d-m-Y', $rwc1['submitted_date']) . '<br>';
                                                    }
                                                } else {
                                                    echo '-';
                                                }
                                            ?>
                                            </td>
                                            <td>
                                            <?php
                                                $rsc2 = $conn->query($sc1);
                                                if ($rsc2->num_rows > 0){
                                                    while ($rwc2 = $rsc2->fetch_assoc()){
                                                        echo getAssessor($conn, $rwc2['assessor_id']);
                                                    }
                                                } else {
                                                    echo '-';
                                                }
                                            ?>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </table>
                                </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/datatables/datatables.min.js"></script>
    <script src="assets/js/initiate-datatables.js"></script>
    <script src="assets/js/script.js"></script>
</body>

</html>
