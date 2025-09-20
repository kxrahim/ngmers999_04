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
                            <div class="page-pretitle">Admin Report</div>
                            <h2 class="page-title">Student Progress Monitoring</h2>
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

                                    <div class="card-body">
                                        <!-- <h5>List students for Cohort <?=getCohort($conn, $_GET['cohort_id'])?></h5> -->
                                        <!-- <p class="card-title">sds</p> -->
                                        <p class="card-title"></p>
                                        <table class="table table-hover table-striped" id="dataTables-example" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>NAME</th>
                                                    <?php
                                                        $st1 = "select *
                                                                from activities
                                                                where status = 1
                                                                order by id asc";
                                                        $rstt1 = $conn->query($st1);
                                                        $k = 0;
                                                        $arrayAct = [];
                                                        while ($rwt1 = $rstt1->fetch_assoc()){
                                                            array_push($arrayAct, $rwt1['id']);
                                                    ?>
                                                        <th style="text-align: center;" title="<?=$rwt1['activity']?>">ACT <?=$k+=1?></th>
                                                    <?php
                                                        }

                                                        //print_r($arrayAct);
                                                    ?>
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

                                                        
                                                ?>
                                                <tr>
                                                    <td><?=$num+=1?></td>
                                                    <td><a href="report-cohort-details.php?s=<?=$rw1['id'];?>" target="_blank"><?=strtoupper($rw1['fullname']);?></a></td>
                                                    <?php
                                                        for ($a=0; $a<12; ++$a){
                                                            $ids = $rw1['id'];
                                                            $actid = $arrayAct[$a];

                                                            //echo $ids . ' - ' . $actid . '<br>';

                                                            $nSession = getAssessmentCompleted($conn, $ids, $actid);
                                                            //$nSession = getAssessmentCompleted($conn, $_GET['s'], $aId);

                                                            $minSession = getMinSession($conn, $actid);    
                                                            $minSession = intval($minSession); //convert                                                      


                                                            if ($nSession == 0){
                                                                $bgColor = 'red';
                                                            } else if ($nSession < $minSession){
                                                                $bgColor = 'orange';
                                                            } else if ($nSession >= $minSession){
                                                                $bgColor = 'green';
                                                            } else {
                                                                
                                                            }
                                                    ?>
                                                        <td style="background-color: <?=$bgColor?>; text-align:center; font-weight: bolder;"><?=$nSession?> | <?=$minSession?></td>
                                                    <?php
                                                        }
                                                    ?>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>

                                        <b>Legend:</b>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul>
                                                    <?php
                                                        $st2 = "select *
                                                                from activities 
                                                                where sorting <= 6 and status = 1
                                                                order by sorting asc";
                                                        $rstt2 = $conn->query($st2);

                                                        $act1 = 0;
                                                        while ($rwt2 = $rstt2->fetch_assoc()){
                                                            $act1 = $act1 + 1;
                                                    ?>
                                                        <li>Activity <?=$act1?>: <?=$rwt2['activity'];?></li>
                                                    <?php
                                                        }
                                                    ?>
                                                </ul>
                                            </div>

                                            <div class="col-md-6">
                                                <ul>
                                                    <?php
                                                        $st3 = "select *
                                                                from activities 
                                                                where sorting >= 7  and status = 1";
                                                        $rstt3 = $conn->query($st3);

                                                        $act3 = 6;
                                                        while ($rwt3 = $rstt3->fetch_assoc()){
                                                    ?>
                                                        <li>Activity <?=$act3+=1?>: <?=$rwt3['activity'];?></li>
                                                    <?php
                                                        }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                    </div>
                                <?php
                                    } else {
                                ?>
                                <div class="card-header">List of available student</div>
                                <div class="card-body">
                                    Please select cohort to display the results.
                                </div>
                                <?php
                                    }
                                ?>
                                
                                
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
