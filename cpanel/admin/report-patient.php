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
        require_once('includes/connection.php');
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
                            <h2 class="page-title">Patient</h2>
                        </div>
                    </div>
                    <div class="row">

                        <?php
                            if ($n == 0){
                        ?>
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">Filter</div>
                                <div class="card-body">
                                    <form action="" method="get">
                                        <div class="col-lg-12 mb-3">
                                            <label for="" class="mb-2">Patient name</label>
                                            <select name="assessor_id" id="assessor_id" class="form-control">
                                                <option value=""></option>
                                                <?php
                                                    $sql01 = "select *
                                                              from patients
                                                              where status = 1
                                                              order by name asc";
                                                    $rst01 = $conn->query($sql01);
                                                    while ($row01 = $rst01->fetch_assoc()){
                                                ?>
                                                <option value="<?=$row01['id'];?>"><?=$row01['name'];?></option>
                                                <?php
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <button class="btn btn-primary" type="submit" name="filter"> Filter </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">List of available patients</div>
                                <div class="card-body">
                                    <p class="card-title"></p>
                                    <table class="table table-hover table-striped" id="dataTables-example" width="100%">
                                        <thead>
                                            <tr>
                                                <th>FULL NAME</th>
                                                <th>ACCEPTED</th>
                                                <th>REJECTED</th>
                                                <th>PENDING</th>
                                                <th>TOTAL</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $sq1 = "SELECT * 
                                                        from patients as a 
                                                        WHERE status = '1'" . $conditions . "
                                                        order by name asc";
                                                $rs1 = $conn->query($sq1);

                                                //echo $sq1;

                                                $num = 0;

                                                while ($rw1 = $rs1->fetch_assoc()){
                                                    // detail
                                                    $assId = $rw1['id'];

                                                    $sn2 = "SELECT * 
                                                            FROM assessments 
                                                            WHERE patient_id = '$assId'";
                                                    $rsn2 = $conn->query($sn2);

                                                    $sAccept = 0;
                                                    $sReject = 0;
                                                    $sPending = 0;
                                                    while ($rws2 = $rsn2->fetch_assoc()){
                                                        if ($rws2['request_status'] == 1){
                                                            $sAccept = $sAccept + 1;
                                                        } else if ($rws2['request_status'] == 2){
                                                            $sReject = $sReject + 1;
                                                        } else if ($rws2['request_status'] == 9){
                                                            $sPending = $sPending + 1;
                                                        } else {

                                                        }

                                                    }
                                            ?>
                                            <tr>
                                                <td><?=strtoupper($rw1['name']);?></td>
                                                <td><a href="?n=3&pid=<?=$rw1['id'];?>" title="View detail."><!--<i class="fa fa-search"></i>--> <?=$sAccept;?> </a></td>
                                                <td><?=$sReject;?></td>
                                                <td><?=$sPending;?></td>
<!--                                                <td>--><?//=getLecturerAssessmentTotal ($conn, $rw1['id']);?><!--</td>-->
                                                <td><?=$sAccept+$sReject+$sPending;?></td>
                                                <td></td>
                                                <!--
                                                <td>
                                                    <a href="?n=1&id=<?=$rw1['oid'];?>" title="Update/Edit"><i class="fa fa-edit"></i></a>
                                                    <a href="?n=3&id=<?=$rw1['oid'];?>" title="Delete"><i class="fa fa-trash" onClick="javascript:return confirm('Are you sure to delete this?');"></i></a>
                                                </td>
                                                -->
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <?php
                                        } else if ($n == 3){
                                            $sn3 = "select *
                                                    from patients
                                                    where id = " . $_GET['pid'];
                                            $rsn3 = $conn->query($sn3);
                                            $rwn3 = $rsn3->fetch_assoc();

                                            if ($rwn3['gender'] = 'M'){
                                                $gender = "Male";
                                            } else {
                                                $gender = "Female";
                                            }
                                    ?>
                                    <div class="col-md-12 col-lg-12 mt-3">
                                        <div class="card">
                                            <div class="card-header">Patient Information</div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        Name:<br>
                                                        <b><?=$rwn3['name'];?></b>
                                                    </div>

                                                    <div class="col-md-2">
                                                        MRN Number:<br>
                                                        <b><?=$rwn3['mrn'];?></b>
                                                    </div>

                                                    <div class="col-md-2">
                                                        Gender:<br>
                                                        <b><?=$gender;?></b>
                                                    </div>
                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col-md-2">
                                                        NRIC / Passport:<br>
                                                        <b><?=$rwn3['idnumber'];?></b>
                                                    </div>

                                                    <div class="col-md-2">
                                                        Phone:<br>
                                                        <b><?=$rwn3['phone'];?></b>
                                                    </div>

                                                    <div class="col-md-2">
                                                        Birthdate:<br>
                                                        <b><?=$rwn3['birthdate'];?></b>
                                                    </div>

                                                    <div class="col-md-1">
                                                        Age:<br>
                                                        <b><?=$rwn3['age'];?></b>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card">
                                            <div class="card-header">Session Information</div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <table class="table table-hover table-striped" id="dataTables-example" width="100%">
                                                        <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th>DATE</th>
                                                            <th>TIME</th>
                                                            <th>ACTIVITY</th>
                                                            <th>OBSERVATION</th>
                                                            <th>STUDENT</th>
                                                            <th>ASSESSOR</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                            $sn4 = "select *
                                                                    from assessments 
                                                                    where patient_id = " . $_GET['pid'] . " and request_status = 1";
                                                            $rsn4 = $conn->query($sn4);

                                                            $i = 0;
                                                            while ($rw4 = $rsn4->fetch_assoc()){
                                                        ?>
                                                        <tr>
                                                            <td><?=++$i;?></td>
                                                            <td><?=date('d-m-Y', $rw4['bookingDate']);?></td>
                                                            <td><?=$rw4['bookingTime'];?></td>
                                                            <td><?=getActivity($conn, $rw4['activity_id']);?></td>
                                                            <td><?=getObservation($conn, $rw4['observation_id']);?></td>
                                                            <td><?=getUser($conn, $rw4['user_id']);?></td>
                                                            <td><?=getUser($conn, $rw4['assessor_id']);?></td>
                                                            <td></td>
                                                        </tr>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                        } else {}
                                    ?>
                                </div>

                            </div>
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