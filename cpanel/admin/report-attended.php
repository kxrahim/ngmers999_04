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

        $title = "eLP Dashboard: Report on Training Attended Record";

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
                            <h2 class="page-title">Training Attendance</h2>
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
                                                <th>COURSE NAME</th>
                                                <th>PARTICIPANT NAME</th>
                                                <th>LOCATION</th>
                                                <th>AGENCIES / UNIT</th>
                                                <th>SESSION DATE</th>
                                                <th>ATTENDANCE STATUS</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $sq1 = "SELECT
                                                            c.id AS course_id,
                                                            c.fullname AS course_name,
                                                            u.id AS user_id,
                                                            CONCAT(u.firstname, ' ', u.lastname) AS student_name,
                                                            ses.id AS session_id,
                                                            ses.sessdate AS session_date,
                                                            st.acronym AS status_acronym,
                                                            st.description AS status_description
                                                        FROM
                                                            elp_attendance a
                                                        JOIN
                                                            elp_course c ON c.id = a.course
                                                        JOIN
                                                            elp_attendance_sessions ses ON ses.attendanceid = a.id
                                                        JOIN
                                                            elp_attendance_log l ON l.sessionid = ses.id
                                                        JOIN
                                                            elp_user u ON u.id = l.studentid
                                                        JOIN
                                                            elp_attendance_statuses st ON st.id = l.statusid
                                                        WHERE
                                                            st.deleted = 0" . $conditions . "
                                                        ORDER BY
                                                            c.fullname, u.lastname, ses.sessdate";
                                                $rs1 = $conn->query($sq1);

                                               // echo $sq1;

                                                $num = 0;
                                                while ($rw1 = $rs1->fetch_assoc()){
                                                    // get user additional field information 
                                                    $location = getUserInfoDataLocation($conn, $rw1['user_id']);
                                                    $agency = getUserInfoDataAgency($conn, $rw1['user_id']);


                                            ?>
                                            <tr>
                                                <!-- <th><?=++$num?></th> -->
                                                <th><?=$rw1['course_name'];?></th>
                                                <th><?=$rw1['student_name'];?></th>
                                                <th><?=$location?></th>
                                                <th><?=$agency?></th>
                                                <td><?=date('d-m-Y', $rw1['session_date']);?></td>
                                                <td><?=$rw1['status_description'];?></td>
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