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

            if ($_GET['cohort_id'] > 0){
                $conditions .= ' and a.cohort_id = ' . $_GET['cohort_id'];
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
                            <h2 class="page-title">Cohort</h2>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">Filter</div>
                                <div class="card-body">
                                    <form action="" method="get">
                                        <div class="col-lg-12 mb-3">
                                            <label for="" class="mb-2">Student Cohort</label>
                                            <select name="cohort_id" id="cohort_id" class="form-control">
                                                <option value=""></option>
                                                <?php
                                                    $sql01 = "select *
                                                              from cohorts
                                                              where status = 1
                                                              order by cohort asc";
                                                    $rst01 = $conn->query($sql01);
                                                    while ($row01 = $rst01->fetch_assoc()){
                                                ?>
                                                <option value="<?=$row01['id'];?>">
                                                    <?=$row01['cohort'];?></option>
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
                                <?php
                                    if (isset($_GET['cohort_id'])){
                                ?>
                                <div class="card-header">List of available students for Cohort <?=getCohort($conn, $_GET['cohort_id'])?></div>
                                <?php
                                    } else {
                                ?>
                                <div class="card-header">List of available student</div>
                                <?php
                                    }
                                ?>
                                
                                <div class="card-body">
                                    <!-- <h5>List students for Cohort <?=getCohort($conn, $_GET['cohort_id'])?></h5> -->
                                    <!-- <p class="card-title">sds</p> -->
                                    <p class="card-title"></p>
                                    <table class="table table-hover table-striped" id="dataTables-example" width="100%">
                                        <thead>
                                            <tr>
                                                <th>STUDENT ID</th>
                                                <th>FULL NAME</th>
                                                <th>EMAIL</th>
                                                <th>COMPLETED</th>
                                                <th>ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $sq1 = "SELECT *
                                                        from users as a
                                                        WHERE role_id = '3'" . $conditions . "
                                                        order by fullname asc";
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
                                                <td><?=strtoupper($rw1['studentId']);?></td>
                                                <td><a href="report-cohort-details.php?s=<?=$rw1['id'];?>" target="_blank"><?=strtoupper($rw1['fullname']);?></a></td>
                                                <td><?=$rw1['email'];?></td>
                                                <td><?=$sCompleted;?></td>
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
