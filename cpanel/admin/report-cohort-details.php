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
                                </div>
                            </div>
                        </div>
                        <!-- <h4>Assessment activities</h4> -->
                        
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">Activities</div>
                                <div class="card-body">
                                    
                                    <table class="table table-hover table-striped" id="dataTables-examplex" width="100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>ACTIVITY</th>
                                                <th width="40%">STATISTIC</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            $sq1 = "select * from activities where status = 1 order by sorting asc";
                                            $rst1 = $conn->query($sq1);
                                            
                                            $num = 0;
                                            $percentStatus = 0;
                                            $nAct = 0;
                                            while ($rw1 = $rst1->fetch_assoc()){
                                                $aId = $rw1['id'];
                                                $nSession = getAssessmentCompleted2($conn, $_GET['s'], $aId);
                                                
                                                $nAct++;

                                                //echo $nAct . '| ' .  $_GET['s'] . ' | ' . $aId . ' | ' . $nSession . '<br>';

                                                if ($nSession < 1){
                                                    $percentStatus = 100;
                                                    $bgColor = 'bg-danger';
                                                } else if ($nSession > 0 and $nSession <= 2){
                                                    $percentStatus = $nSession * 16;
                                                    $bgColor = 'bg-warning';
                                                } else if ($nSession > 2 and $nSession <= 5){
                                                    $percentStatus = $nSession * 16;
                                                    $bgColor = '';
                                                } else {
                                                    $percentStatus = 100;
                                                    $bgColor = 'bg-success';
                                                }
                                        ?>
                                            <tr>
                                                <td><?=++$num?></td>
                                                <td><?=$rw1['activity']?> <?=$nSession?></td>
                                                <td>
                                                <div class="progress">
                                                    <div class="progress-bar progress-bar-striped <?=$bgColor?>" role="progressbar" style="width: <?=$percentStatus?>%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" title="<?=$nSession?> activity"></div>
                                                </div>
                                                </td>
                                            </tr>
                                        <?php
                                            }
                                        ?>
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
