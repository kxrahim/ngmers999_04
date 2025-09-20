<?php
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
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


            if (!empty($_GET['date_from']))
            {
                $dateFrom = strtotime($_GET['date_from']);
                if (!empty($_GET['date_to']))
                {
                    $dateTo = strtotime($_GET['date_to']);
                    $conditions .= ' and (bookingDate >= ' . $dateFrom . " and a.bookingDate <= " . $dateTo . ")" ;
                }
                else
                {
                    $conditions .= ' and a.bookingDate >= ' . $dateFrom;
                }

            }

            if ($_GET['activity_id'] > 0){
                $conditions .= ' and a.activity_id = ' . $_GET['activity_id'];
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
                            <div class="page-pretitle">Session</div>
                            <h2 class="page-title">Session Detail</h2>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">Detail selected session</div>
                                <div class="card-body">
                                    <script src="https://cdn.tiny.cloud/1/hd75qnyp7pt3rd5yw7e8dqfvfa7496wmrzhlpi0mv9stwfxr/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
                                    <form action="" method="POST">
                                        <table id="example11" class="table table-bordered table-striped">
                                            <thead>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $sqi1 = "select *
                                                     from assessments
                                                     where id = '" . $_GET['id'] . "'";
                                            $rsi1 = $conn->query($sqi1);


                                            $i = 0;
                                            $rwi1 = $rsi1->fetch_assoc();

                                            $student_id   = $rwi1['user_id'];
                                            $activity_id  = $rwi1['activity_id'];

                                            $reqStatus = '';
                                            $btnStatus = '';
                                            $icon = '';

                                            if ($rwi1['request_status'] == '1'){
                                                $reqStatus = '<span style="color:#9CC862;">ACCEPTED</span>';
                                                $btnStatus = 'btn-success';
                                                $icon = 'fa fa-check';
                                            } else if ($rwi1['request_status'] == '2') {
                                                $reqStatus = '<span style="color:#FFC107;">PENDING FOR APPROVAL</span>';
                                                $btnStatus = 'btn-warning';
                                                $icon = 'fa fa-edit';
                                            } else if ($rwi1['request_status'] == '9'){
                                                $reqStatus = '<span style="color:#FF1900;">REJECT</span>';
                                                $btnStatus = 'btn-danger';
                                                $icon = 'fa fa-ban';
                                            } else {

                                            }

                                            // get assessment info
                                            $sqi2 = "select *
                                                     from assessment_assessors
                                                     where assessment_id = '" . $_GET['id'] . "'";
                                            $rsi2 = $conn->query($sqi2);
                                            $rwi2 = $rsi2->fetch_assoc();

                                            //print_r($rwi2);

                                            ?>

                                            <tr>
                                                <td>Date</td>
                                                <td>
                                                    <?=date('d-m-Y', $rwi1['bookingDate']);?>
                                                    <input type="hidden" class="form-control" name="id" value="<?=$rwi1['id'];?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Time</td>
                                                <td><?=$rwi1['bookingTime'];?></td>
                                            </tr>
                                            <tr>
                                                <td>Activity</td>
                                                <td><?=getActivity($conn, $rwi1['activity_id']);?></td>
                                            </tr>
                                            <tr>
                                                <td>Observation</td>
                                                <td><?=getObservation($conn, $rwi1['observation_id']);?></td>
                                            </tr>
                                            <tr>
                                                <td>Entrustment level for the concerned activity</td>
                                                <td><?=getObservationLevel($conn, $rwi1['observation_id']);?></td>
                                            </tr>
                                            <tr>
                                                <td>Student name</td>
                                                <td><?=getUser($conn, $rwi1['user_id']);?></td>
                                            </tr>
                                            <?php  ?>
                                            </tbody>
                                        </table>

                                        <!-- ------------------- evaluation part ------------------ -->
                                        <?php
                                            // check if assessment already assess
                                            $sn1 = "select *
                                                    from assessment_assessors
                                                    where assessment_id = '" . $_GET['id'] . "'";
                                            $rsn1 = $conn->query($sn1);

                                            if ($rsn1->num_rows > 0){
                                        ?>
                                        <h5 for="">Assessment rubrics</h5>
                                        <table id="example11" class="table table-bordered table-striped table-hover">
                                            <thead class="thead-dark">
                                            <tr>
                                                <th style="vertical-align: middle;">
                                                    Domains<br>
                                                    <i>(check the appropriate domain(s) to a particular activity)</i>
                                                </th>
                                                <th>Outcome</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <!-- Domain 1 -->
                                            <tr>
                                                <td style="vertical-align: middle;"><b>Knowledge and understanding </b><br>
                                                    [Acquire and apply the knowledge of basic clinical and dental sciences to ensure effective chair-side assisting and patient management]
                                                    <br><a href="" data-toggle="modal" data-target=".domain1">Domain details</a>
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <?php
                                                    $domain1 = getOutcome($conn, $rwi2['domain1']);
                                                    echo $domain1[0];
                                                    echo '<br>[' . $domain1[1] . ']';
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" style="background-color: black; height: 1px;" class="saperator">
                                                </td>
                                            </tr>
                                            <!-- domain 2 -->
                                            <tr>
                                                <td style="vertical-align: middle;"><b>Cognitive skills/Critical thinking, problem-solving</b><br>
                                                    [Utilize critical thinking and problem solving skills in patient care decision making]
                                                    <br><a href="" data-toggle="modal" data-target=".domain2">Domain details</a>
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <?php
                                                    $domain2 = getOutcome($conn, $rwi2['domain2']);
                                                    echo $domain2[0];
                                                    echo '<br>[' . $domain2[1] . ']';
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" style="background-color: black; height: 1px;" class="saperator">
                                                </td>
                                            </tr>

                                            <!-- domain 3 -->
                                            <tr>
                                                <td style="vertical-align: middle;"><b>Functional Work Skills/ Practical skills </b><br>
                                                    [Demonstrate skills in patient management and clinical care]
                                                    <br><a href="" data-toggle="modal" data-target=".domain3">Domain details</a>
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <?php
                                                    $domain3 = getOutcome($conn, $rwi2['domain3']);
                                                    echo $domain3[0];
                                                    echo '<br>[' . $domain3[1] . ']';
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" style="background-color: black; height: 1px;" class="saperator">
                                                </td>
                                            </tr>

                                            <!-- domain 4 -->
                                            <tr>
                                                <td style="vertical-align: middle;"><b>Functional Work Skills/Communication and interpersonal skill</b><br>
                                                    [Communicate effectively with peers in the dental and other health professions, patients and community]
                                                    <br><a href="" data-toggle="modal" data-target=".domain4">Domain details</a>
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <?php
                                                    $domain4 = getOutcome($conn, $rwi2['domain4']);
                                                    echo $domain4[0];
                                                    echo '<br>[' . $domain4[1] . ']';
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" style="background-color: black; height: 1px;" class="saperator">
                                                </td>
                                            </tr>

                                            <!-- domain 5 -->
                                            <tr>
                                                <td style="vertical-align: middle;"><b>Ethics and Professionalism</b>
                                                    <br>
                                                    [Adhere to the legal, ethical principles and the professional code of conduct in patient care]
                                                    <br><a href="" data-toggle="modal" data-target=".domain5">Domain details</a>
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <?php
                                                    $domain5 = getOutcome($conn, $rwi2['domain5']);
                                                    echo $domain5[0];
                                                    echo '<br>[' . $domain5[1] . ']';
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" style="background-color: black; height: 1px;" class="saperator">
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <h5>Indicate the level of supervision required (for the next case)</h5>
                                                Based on my observation(s), I suggest for this EPA(observable patient encounter) the student may be ready after the next review to be:
                                                <br><br>
                                                <?php
                                                // display epas
                                                $sql4 = "select *
                                                         from levels
                                                         where id = '" . $rwi2['epa_id'] . "'
                                                         order by id asc";
                                                $result4 = $conn->query($sql4);
                                                while ($row4 = $result4->fetch_assoc()){
                                                    ?>
                                                    <div class="input-group mb-3 col-md-12" style="margin-left: -0.6em;">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                <input type="radio" aria-label="Checkbox for following text input" name="epa_id" value="<?=$row4['id'];?>" checked>
                                                            </div>
                                                        </div>
                                                        <input type="text" class="form-control" aria-label="Text input with checkbox" value="<?=$row4['level'];?>" disabled>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <h5 for="">Outcome overall</h5>
                                                    <label for="">
                                                        <?php
                                                        $overalOutcome = getOutcome($conn, $rwi2['epa_id']);
                                                        echo $overalOutcome[0];
                                                        echo '<br>[' . $overalOutcome[1] . ']';
                                                        ?>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <h5 for="">Duration of observation</h5>
                                                    <label for=""><?=$rwi2['duration'];?> minutes</label>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>
                                        <?php
                                            } else {
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h5 style="color: #FF0000;">This assessment not completed.</h5>
                                            </div>
                                        </div>
                                        <?php
                                            }
                                        ?>
                                        <!-- ------------- closed assessment area ------------------ -->

                                        <?php
                                        if ($rwi1['feedback_id'] > 0){
                                            $sql3 = "SELECT *
                                                     FROM assessment_feedbacks
                                                     WHERE id = '" . $rwi1['feedback_id'] . "'";
                                            //echo $sql3;
                                            $rst3 = $conn->query($sql3);
                                            $rwi3 = $rst3->fetch_assoc();
                                            ?>
                                            <h5>Assessor feedback</h5>
                                            Provide feedback on the performance (correspondence to competency domains relevant to his EPA; strengths; weaknesses and how can the student improve)
                                            <br><br>
                                            <table id="example11" class="table table-bordered table-striped table-hover">
                                                <thead class="thead-dark">
                                                <tr>
                                                    <th style="width: 15%;">Criteria</th>
                                                    <th>Feedback</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <th>Knowledge & Understanding</th>
                                                    <td><i><?=$rwi3['feedback1'];?></i></td>
                                                </tr>
                                                <tr>
                                                    <th>Cognitive Skills – Critical thinking/problem solving</th>
                                                    <td><i><?=$rwi3['feedback2'];?></i></td>
                                                </tr><tr>
                                                    <th>Functional Work Skills – Practical skills</th>
                                                    <td><i><?=$rwi3['feedback3'];?></i></td>
                                                </tr><tr>
                                                    <th>Functional Work Skills – Communication skills</th>
                                                    <td><i><?=$rwi3['feedback4'];?></i></td>
                                                </tr>
                                                <tr>
                                                    <th>Ethics and Professionalism</th>
                                                    <td><i><?=$rwi3['feedback5'];?></i></td>
                                                </tr>
                                                </tbody>
                                            </table>

                                        <?php } else { ?>

                                        <?php } ?>

                                        <?php if ($rwi1['reflection_id'] < 1){ ?>
                                            <br>
                                            <a href="index.php" class="btn btn-secondary">CANCEL</a>
                                            <?php
                                        } else {
                                            $sql5 = "SELECT *
                                                     FROM assessment_reflections
                                                     WHERE assessment_id = '" . $rwi1['id'] . "'";
                                            $rst5 = $conn->query($sql5);
                                            $rwi5 = $rst5->fetch_assoc();
                                            ?>
                                            <hr>
                                            <h5>Student reflection</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <b>Reflection on dental aspects</b><br>
                                                    <i><?=$rwi5['dentalAspect'];?></i>
                                                </div>
                                                <div class="col-md-6">
                                                    <b>Reflection on professionalism</b><br>
                                                    <i><?=$rwi5['professionalism'];?></i>
                                                </div>
                                            </div>
                                            <br>

                                            <a href="index.php" class="btn btn-secondary"><i class="fa fa-backward"></i> Back </a>
                                        <?php } ?>
                                        <br>
                                    </form>
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