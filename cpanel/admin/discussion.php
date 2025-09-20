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

        if (isset($_GET['id'])){
            $id = $_GET['id'];
        } else {
            $id = 0;
        }

        if (isset($_GET['r'])){
            $r = $_GET['r'];
        } else {
            $r = 0;
        }

        if (isset($_POST['btnReply'])){
            // Array ( [assessment_discussion_id] => 2 [activity_id] => 1 [feedback] => yes. i have complete that one. [btnReply] => )
            $assessment_discussion_id = $_POST['assessment_discussion_id'];
            $activity_id              = $_POST['activity_id'];
            $feedback                 = mysqli_real_escape_string($conn, $_POST['feedback']);
            $user_id                  = $_SESSION['user_id'];

            $sn1 = "INSERT INTO assessment_discussion_replies (assessment_discussion_id,
                                                                 activity_id,
                                                                 user_id,
                                                                 feedback)
                                                       VALUES ('$assessment_discussion_id',
                                                               '$activity_id',
                                                               '$user_id',
                                                               '$feedback')";

            //echo $sn1;
            //exit;

            if ($conn->query($sn1) === TRUE){
                echo "<script type='text/javascript'>
                          alert('Feedback successfully submit.');
                          window.location='index.php?s=1&id=" .$activity_id. "&d=" .$assessment_discussion_id. "&r=1';
                      </script>";
                exit();
            }

            //print_r($_POST);
            //exit;
        }
    ?>

    <script src="//cdn.ckeditor.com/4.16.1/standard/ckeditor.js"></script>

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
                <?php
                    if ($_SESSION['role'] == 1){
                ?>
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 page-header">
                            <div class="page-pretitle">Overview</div>
                            <h2 class="page-title">Student Response System</h2>
                        </div>
                    </div>

                    <!-- sessions and assessment -->
                    <?php
                        if ($id == 0) {
                    ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="content">
                                            <div class="head">
<!--                                                <h5 class="mb-0">Discussion</h5>-->
                                                <p class="text-muted">All available discussion</p>
                                            </div>
                                            <div class="canvas-wrapper">
                                                <table id="dataTables-example2" class="table table-hover table-striped table-responsive">
                                                    <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>ACTIVITY</th>
                                                        <th>TOPIC</th>
                                                        <th>TOTAL REPLIES</th>
                                                        <th>STATUS</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $sqi1 = "select *
                                                             from activities
                                                             where status = '1'
                                                             order by id";
                                                    $rsi1 = $conn->query($sqi1);

                                                    $i = 0;
                                                    while ($rwi1 = $rsi1->fetch_assoc()){
                                                        //print_r($rwi1);

                                                        ?>
                                                        <tr>
                                                            <td><?=++$i;?></td>
                                                            <td><?=$rwi1['activity'];?></td>
                                                            <td><?=getTotalTopic($conn, $rwi1['id']);?></td>
                                                            <td><?=getTotalReply($conn, $rwi1['id']);?></td>
                                                            <td style="width: 150px;">
                                                                <a href="?s=1&id=<?=$rwi1['id'];?>" class="btn btn-primary" title="View detail"><i class="fa fa-binoculars"></i></a>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th>ACTIVITY</th>
                                                        <th>TOPIC</th>
                                                        <th>TOTAL REPLIES</th>
                                                        <th>STATUS</th>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                    <!-- display detail discussion -->
                    <?php
                        if ($id > 0) {
                    ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="content">
                                            <div class="head">
                                                <h5 class="mb-0">Activity</h5>
                                                <p class="text-muted"><?=getActivity($conn, $id);?></p>
                                            </div>
                                            <div class="canvas-wrapper">
                                                <table id="dataTables-example2" class="table table-bordered table-striped">
                                                    <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>TOPIC</th>
                                                        <th>TOTAL REPLIES</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $sqi1 = "select *
                                                             from assessment_discussions
                                                             where status = '1' and assessment_id = '$id'
                                                             order by id";
                                                    $rsi1 = $conn->query($sqi1);

                                                    $i = 0;
                                                    while ($rwi1 = $rsi1->fetch_assoc()){
                                                        //print_r($rwi1);

                                                        ?>
                                                        <tr>
                                                            <td><?=++$i;?></td>
                                                            <td><?=$rwi1['discussion'];?></td>
                                                            <td><?=getDiscussionReply($conn, $rwi1['id']);?></td>
                                                            <td style="width: 150px; text-align: center;">
                                                                <a href="?s=1&id=<?=$rwi1['activity_id'];?>&r=<?=$rwi1['id'];?>" class="btn btn-primary" title="View detail"><i class="fa fa-search"></i></a>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <th></th>
                                                        <th>TOPIC</th>
                                                        <th>TOTAL REPLIES</th>
                                                    </tr>
                                                    </tfoot>
                                                </table>

                                                <?php
                                                if ($r > 0){
                                                    ?>
                                                    <form action="" method="post">
                                                        <table id="example1" class="table table-bordered table-striped mt-5">
                                                            <tr>
                                                                <td colspan="1">Reply discussion for topic:<br><h4><?=getDiscussion($conn, $_GET['r']);?></h4></td>
                                                            </tr>
                                                            <?php
                                                            $sn2 = "select *
                                                                    from assessment_discussion_replies
                                                                    where assessment_discussion_id = " .$_GET['r']. " and status = 1";
                                                            $rsn2 = $conn->query($sn2);

                                                            while ($rwn2 = $rsn2->fetch_assoc()){
                                                                ?>
                                                                <tr>
                                                                    <td>

                                                                        <h6><?=getUser($conn, $rwn2['user_id']);?> <span class="small font-italic" style="color: #00A000;"><?=$rwn2['created_date'];?></span></h6>
                                                                        <h5><?php echo $rwn2['feedback'];?></h5>

                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            }
                                                            ?>

                                                            <tr>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <label for="exampleFormControlTextarea1">Reply</label>
                                                                        <input type="hidden" value="<?=$_GET['r'];?>" name="assessment_discussion_id">
                                                                        <input type="hidden" value="<?=$_GET['id'];?>" name="activity_id">
                                                                        <textarea class="form-control" id="editor" rows="3" name="feedback"></textarea>
                                                                        <button class="btn btn-success mt-3" name="btnReply" type="submit">Sent</button>
                                                                    </div>
                                                                </td>
                                                            </tr>


                                                        </table>
                                                    </form>
                                                    <?php
                                                }
                                                ?>

                                            </div>
                                            <a href="discussion.php" class="btn btn-secondary"> Back to discussion list </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                </div>
                <?php
                    } else if ($_SESSION['role'] == 2){
                        echo 'ini dia';
                    } else {

                    }
                ?>
            </div>

            <p></p>
            <p></p>
            <p></p>

        </div>


    </div>
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/datatables/datatables.min.js"></script>
    <script src="assets/js/initiate-datatables.js"></script>
    <script src="assets/js/script.js"></script>

    <script>
        (function() {
            'use strict';

            $('#dataTables-example1').DataTable({
                responsive: true,
                pageLength: 20,
                lengthChange: false,
                searching: true,
                ordering: true
            });

            $('#dataTables-example2').DataTable({
                responsive: true,
                pageLength: 20,
                lengthChange: false,
                searching: true,
                ordering: true
            });

        })();
    </script>

    <script>
        CKEDITOR.replace('editor');
    </script>

</body>

</html>
