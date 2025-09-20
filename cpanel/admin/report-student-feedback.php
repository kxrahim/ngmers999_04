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
                            <h2 class="page-title">Assessment Feedback</h2>
                        </div>
                    </div>

                    <!-- get student information -->
                    <?php
                        $cid = $_GET['id'];
                        $sq3 = "SELECT *
                                FROM users
                                WHERE id = " . $cid;
                        $rs3 = $conn->query($sq3);
                        $rw3 = $rs3->fetch_assoc();

                        //get total feedback 

                    ?>
                    <div class="row">                        
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">Student Information</div>
                                <div class="card-body">
                                    <div class="row mb-1">
                                        <div class="col-md-2">
                                            Name
                                        </div>
                                        <div class="col-md-10">
                                            : <?=$rw3['fullname']?>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-2">
                                            Email
                                        </div>
                                        <div class="col-md-10">
                                            : <?=$rw3['email']?>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-2">
                                            Cohort
                                        </div>
                                        <div class="col-md-10">
                                            : <?=getCohort($conn, $rw3['cohort_id'])?>
                                        </div>
                                    </div>
                                    <!-- <div class="row">
                                        <div class="col-md-2">
                                            Total Feedback
                                        </div>
                                        <div class="col-md-10">
                                            : Name
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12 col-lg-12 mt-3">
                            <a class="btn btn-secondary" href="report-student.php">
                                << Back to Student Report
                            </a>
                        </div>
                        
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">List of Assessment Feedback</div>
                                <div class="card-body">
                                    <p class="card-title"></p>
                                    <table class="table table-hover table-striped" id="dataTables-example" width="100%">
                                        <thead>
                                            <tr>
                                                <th>ASSESSMENT</th>
                                                <th class="text-left">Knowledge & Understanding</th>
                                                <th class="text-left">Cognitive Skills – Critical thinking/problem solving</th>
                                                <th class="text-left">Functional Work Skills – Practical skills</th>
                                                <th class="text-left">Functional Work Skills – Communication skills</th>
                                                <th class="text-left">Ethics and Professionalism</th>
                                                <th class="text-left">General</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                
                                                $sq1 = "SELECT *, a.id as aid
                                                        FROM assessments AS a
                                                        JOIN activities AS b ON a.activity_id = b.id
                                                        WHERE a.user_id = '$cid' and a.feedback_id > 0
                                                        ORDER BY a.id ASC";
                                                $rs1 = $conn->query($sq1);
                                                
                                                if ($rs1->num_rows > 0) {
                                                    while ($rw1 = $rs1->fetch_assoc()){
                                                    // detail
                                                    $sq2 = "SELECT *
                                                            FROM assessment_feedbacks
                                                            WHERE assessment_id = " . $rw1['aid'];
                                                    $rs2 = $conn->query($sq2);

                                                    if ($rs2->num_rows > 0){
                                                        $rw2 = $rs2->fetch_assoc();
                                                    } else {
                                                        $rw2['feedback1'] = '-';
                                                        $rw2['feedback2'] = '-';
                                                        $rw2['feedback3'] = '-';
                                                        $rw2['feedback4'] = '-';
                                                        $rw2['feedback5'] = '-';
                                                        $rw2['general'] = '-';
                                                    }
                                                    
                                            ?>
                                            <tr>                                                
                                                <td class=""><?=$rw1['activity']?></td>
                                                <td class="text-left"><?=$rw2['feedback1']?></td>
                                                <td class="text-left"><?=$rw2['feedback2']?></td>
                                                <td class="text-left"><?=$rw2['feedback3']?></td>
                                                <td class="text-left"><?=$rw2['feedback4']?></td>
                                                <td class="text-left"><?=$rw2['feedback5']?></td>
                                                <td class="text-left"><?=$rw2['general']?></td>
                                            </tr>
                                            <?php   } // close while
                                                } // close num
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