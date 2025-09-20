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

        $title = "eLP Dashboard: Report on Training Schedule";

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
            // Array ( [agency] => UNIT APM [agency_id] => 4 [coursename] => [course_id] => [btnFilter] => )
            if (!empty($_POST['agency_id'])) {
                if ($_POST['agency_id'] > 0) {
                    $conditions .= ' and ch.id = ' . $_POST['agency_id'];  
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
                            <h2 class="page-title">Training Schedule</h2>
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
                                                <label for="" class="form-label">Agency / Unit</label>
                                                <input type="text" class="form-control" id="agency_name" name="agency">
                                                <!-- Hidden input to store the student ID -->
                                                <input type="hidden" id="agency_id" name="agency_id">
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

                                        <!-- <div class="row">
                                            <div class="col-lg-6 mb-3">
                                                <label for="" class="mb-2">From</label>
                                                <input type="date" class="form-control" name="date_from">
                                            </div>
                                            <div class="col-lg-6 mb-3">
                                                <label for="" class="mb-2">To</label>
                                                <input type="date" class="form-control" name="date_to">
                                            </div>
                                        </div>

                                        <div class="col-lg-12 mb-3">
                                            <label for="" class="mb-2">Activity</label>
                                            <select name="activity_id" id="activity_id" class="form-control">
                                                <option value=""></option>
                                                
                                            </select>
                                        </div> -->
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
                                                <th>AGENCY/UNIT</th>
                                                <th>COURSE NAME</th>
                                                <th>TRAINING DATE</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $sq1 = "SELECT
                                                            ch.id AS cohort_id,
                                                            ch.name AS cohort_name,
                                                            c.id AS course_id,
                                                            c.fullname AS course_name,
                                                            -- FROM_UNIXTIME(c.startdate) AS training_start_date
                                                            c.startdate AS training_start_date
                                                        FROM
                                                            elp_cohort ch
                                                        JOIN
                                                            elp_cohort_members cm ON cm.cohortid = ch.id
                                                        JOIN
                                                            elp_user u ON u.id = cm.userid
                                                        JOIN
                                                            elp_user_enrolments ue ON ue.userid = u.id
                                                        JOIN
                                                            elp_enrol e ON e.id = ue.enrolid
                                                        JOIN
                                                            elp_course c ON c.id = e.courseid
                                                        WHERE ch.visible = 1 " . $conditions . "
                                                        GROUP BY
                                                            ch.id, c.id
                                                        ORDER BY
                                                            ch.name, c.fullname";
                                                $rs1 = $conn->query($sq1);

                                               // echo $sq1;

                                                $num = 0;
                                                while ($rw1 = $rs1->fetch_assoc()){
                                                    // get user additional field information 


                                            ?>
                                            <tr>
                                                <!-- <th><?=++$num?></th> -->
                                                <th><?=$rw1['cohort_name'];?></th>
                                                <th><?=$rw1['course_name'];?></th>
                                                <td><?=date('d-m-Y', $rw1['training_start_date'])?></td>
                                                <td></td>
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
            $('#agency_name').keyup(function () {
                var query = $(this).val();
                if (query != '') {
                    // AJAX call to fetch suggestions
                    $.ajax({
                        url: "search-agency.php",
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
                $('#agency_name').val(name);
                $('#agency_id').val(id);

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