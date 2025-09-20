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

        $title = "eLP Dashboard: Report on Participant Training Hours / Statistics";

        require_once('includes/main-header.php'); 
        require_once('includes/connection.php');
        require_once('includes/functions.php');
        require_once('includes/datatable-style.php');


        if (isset($_GET['n'])){
            $n = $_GET['n'];
        } else {
            $n = 0;
        }

        // for filtering
        $conditions = '';
        if (isset($_POST['btnFilter'])){
            // Array ( [rujukanKlien] => RAHIM KNOWIX [student_id] => 3 [course] => 8 [btnFilter] => )
            if (!empty($_POST['student_id'])) {
                if ($_POST['student_id'] > 0) {
                    $conditions .= ' and u.id = ' . $_POST['student_id'];  
                } 
            }

            if (!empty($_POST['course'])) {
                if ($_POST['course'] > 0) {
                    $conditions .= ' and c.id = ' . $_POST['course'];  
                } 
            }

            // echo $conditions;
            // print_r($_POST);
            // exit;          

        }
        
    ?>

    <style>
      #hiddenBlock {
          display: <?php echo $isChecked ? 'block' : 'none'; ?>;
      }

      #suggestions {
          border: 0px solid #ccc;
          max-height: 150px;
          overflow-y: auto;
      }      

      .suggestion-item {
          padding: 8px;
          cursor: pointer;
      }
      .suggestion-item:hover {
          background-color: #f0f0f0;
      }

      /* ----- course filter ------ */
      #course_suggestions {
          border: 0px solid #ccc;
          max-height: 150px;
          overflow-y: auto;
      }

      .course_suggestion-item {
          padding: 8px;
          cursor: pointer;
      }
      .course_suggestion-item:hover {
          background-color: #f0f0f0;
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
                            <h2 class="page-title">Participant Training Hours / Statistics</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">Filter</div>
                                <div class="card-body">
                                    <form action="" method="post">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="" class="form-label">Participant Name</label>
                                                <input type="text" class="form-control" id="student_name" name="rujukanKlien">
                                                <!-- Hidden input to store the student ID -->
                                                <input type="hidden" id="student_id" name="student_id">
                                                <div id="suggestions"></div>
                                            </div>
                                        </div>

                                        <?php
                                            $courseOptions = [];
                                            $res = $conn->query("SELECT id, fullname FROM elp_course ORDER BY fullname");
                                            while ($row = $res->fetch_assoc()) {
                                                $courseOptions[$row['id']] = $row['fullname'];
                                            }
                                        ?>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="" class="form-label">Course Name</label>
                                                <select name="course" id="" class="form-control">
                                                    <option value=""></option>
                                                    <?php
                                                        foreach ($courseOptions as $id => $name) {
                                                            $selected = ($id == $selectedCourse) ? "selected" : "";
                                                            echo "<option value='$id' $selected>" . htmlspecialchars($name) . "</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <button class="btn btn-primary" type="submit" name="btnFilter"> Filter </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">List of available data</div>
                                <div class="card-body">
                                    <p class="card-title"></p>
                                    <table class="table table-hover" id="example1" width="100%">
                                        <thead>
                                            <tr>
                                                <!-- <th>#</th> -->
                                                <th>PARTICIPANT NAME</th>
                                                <th>AGENCIES / UNIT</th>
                                                <th>COURSE NAME</th>
                                                <th>TIME SPENT</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $sq1 = "SELECT 
                                                            u.id AS user_id,
                                                            CONCAT(u.firstname, ' ', u.lastname) AS 'Participant Name',
                                                            ch.name AS 'Cohort Name',
                                                            g.name AS 'Group Name',
                                                            c.id AS course_id,
                                                            c.fullname AS 'Course Name',
                                                            ROUND(SUM(session_time) / 60, 2) AS 'Time Spent'
                                                        FROM (
                                                            SELECT 
                                                                l.userid,
                                                                l.courseid,
                                                                l.timecreated,
                                                                LEAD(l.timecreated) OVER (PARTITION BY l.userid, l.courseid ORDER BY l.timecreated) AS next_time,
                                                                CASE 
                                                                    WHEN LEAD(l.timecreated) OVER (PARTITION BY l.userid, l.courseid ORDER BY l.timecreated) - l.timecreated < 1800
                                                                    THEN LEAD(l.timecreated) OVER (PARTITION BY l.userid, l.courseid ORDER BY l.timecreated) - l.timecreated
                                                                    ELSE 0
                                                                END AS session_time
                                                            FROM elp_logstore_standard_log l
                                                            WHERE l.courseid != 1
                                                        ) sessions
                                                        JOIN elp_user u ON u.id = sessions.userid
                                                        JOIN elp_course c ON c.id = sessions.courseid

                                                        -- Only include users with the student role
                                                        JOIN elp_user_enrolments ue ON ue.userid = u.id
                                                        JOIN elp_enrol e ON e.id = ue.enrolid AND e.courseid = c.id
                                                        JOIN elp_role_assignments ra ON ra.userid = u.id
                                                        JOIN elp_context ctx ON ctx.id = ra.contextid AND ctx.contextlevel = 50 AND ctx.instanceid = c.id
                                                        JOIN elp_role r ON r.id = ra.roleid AND r.shortname = 'staff'

                                                        LEFT JOIN
                                                            elp_groups_members gm ON gm.userid = u.id
                                                        LEFT JOIN
                                                            elp_groups g ON g.id = gm.groupid AND g.courseid = c.id
                                                        LEFT JOIN
                                                            elp_cohort_members chm ON chm.userid = u.id
                                                        LEFT JOIN
                                                            elp_cohort ch ON ch.id = chm.cohortid
                                                        WHERE u.suspended = 0 " . $conditions . "
                                                        GROUP BY u.id, c.id
                                                        ORDER BY u.id, c.id";
                                                $rs1 = $conn->query($sq1);

                                                //echo $sq1;

                                                $num = 0;

                                                if ($rs1->num_rows > 0) {
                                                while ($rw1 = $rs1->fetch_assoc()){
                                                    // get user additional field information 
                                                    //$location = getUserInfoDataLocation($conn, $rw1['user_id']);
                                                    //$agency = getUserInfoDataAgency($conn, $rw1['user_id']);
                                                    $minutes = $rw1['Time Spent'];

                                                    $d = floor ($minutes / 1440);
                                                    $h = floor (($minutes - $d * 1440) / 60);
                                                    $m = $minutes - ($d * 1440) - ($h * 60);
                                                    $m = round($m);


                                            ?>
                                            <tr>
                                                <!-- <th><?=++$num?></th> -->
                                                <th><?=$rw1['Participant Name'];?></th>
                                                <th><?=$rw1['Cohort Name'];?></th>
                                                <td><?=$rw1['Course Name'];?></td>
                                                <td><?=$d . ' day(s) ' . $h . ' hour ' . $m . ' minutes';?></td>
                                            </tr>
                                            <?php } 
                                                } else {
                                            ?>
                                                <tr>
                                                    <td colspan="4">No data found</td>
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

    <?php
        require_once ('includes/datatable-script.php');
    ?>

    <script type="text/javascript">
        $(document).ready(function () {
            // On keyup event in the student_name input
            $('#student_name').keyup(function () {
                var query = $(this).val();
                if (query != '') {
                    // AJAX call to fetch suggestions
                    $.ajax({
                        url: "search-participant.php",
                        method: "POST",
                        data: { query: query },
                        success: function (data) {
                            $('#suggestions').fadeIn();
                            $('#suggestions').html(data);
                        }
                    });
                } else {
                    $('#suggestions').fadeOut();
                }
            });

            // When clicking on a suggestion
            $(document).on('click', '.suggestion-item', function () {
                var name = $(this).text();
                var id = $(this).data('id');

                // Fill the input fields
                $('#student_name').val(name);
                $('#student_id').val(id);

                // Hide the suggestions dropdown
                $('#suggestions').fadeOut();
            });

            // --------------- course name -----------------
            // On keyup event in the student_name input
            $('#course_name').keyup(function () {
                var query = $(this).val();
                if (query != '') {
                    // AJAX call to fetch suggestions
                    $.ajax({
                        url: "search-course.php",
                        method: "POST",
                        data: { query: query },
                        success: function (data) {
                            $('#course_suggestions').fadeIn();
                            $('#course_suggestions').html(data);
                        }
                    });
                } else {
                    $('#course_suggestions').fadeOut();
                }
            });

            // When clicking on a suggestion
            $(document).on('click', '.course_suggestion-item', function () {
                var name = $(this).text();
                var id = $(this).data('id');

                // Fill the input fields
                $('#course_name').val(name);
                $('#course_id').val(id);

                // Hide the suggestions dropdown
                $('#course_suggestions').fadeOut();
            });
        }); // close document function 
    </script>
</body>

</html>