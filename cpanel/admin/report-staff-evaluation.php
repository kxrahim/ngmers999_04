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

            if ($_GET['student_id'] > 0){
                $conditions .= ' and u.id = ' . $_GET['student_id'];
            }

            if ($_GET['course_id'] > 0){
                $conditions .= ' and c.id = ' . $_GET['course_id'];
            }

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
                            <h2 class="page-title">Training Evaluation - Staff</h2>
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
                                                <label for="" class="form-label">Course Name</label>
                                                <select class="form-select" name="courseid">
                                                    <option value="0">  </option>
                                                    <?php
                                                        $sql2 = "SELECT
                                                                    c.id AS 'Course ID',
                                                                    c.fullname AS 'Course Name'
                                                                FROM
                                                                    elp_feedback_completed fc
                                                                JOIN
                                                                    elp_feedback f ON f.id = fc.feedback
                                                                JOIN
                                                                    elp_course c ON c.id = f.course
                                                                JOIN
                                                                    elp_user u ON u.id = fc.userid
                                                                JOIN
                                                                    elp_feedback_item i ON i.feedback = f.id
                                                                JOIN
                                                                    elp_feedback_value v ON v.completed = fc.id AND v.item = i.id
                                                                WHERE
                                                                    f.course != 1  -- Exclude site-level feedback
                                                                    AND fc.userid != 0
                                                                GROUP BY c.id
                                                                ORDER BY
                                                                    c.fullname, f.name, u.lastname, i.id";
                                                        $result2 = $conn->query($sql2);

                                                        while ($row2= $result2->fetch_assoc()){
                                                            
                                                    ?>
                                                    <option value="<?=$row2['Course ID']?>"> <?=$row2['Course Name']?> </option>
                                                    <?php } // close while ?>
                                                </select>
                                            </div>
                                        </div>
                                        <button class="btn btn-primary" type="submit" name="btnFilter"> Filter </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                            if (isset($_POST['btnFilter'])) {
                                $courseid = $_POST['courseid'];
                                // Validate input
                                if (!isset($_POST['courseid']) || !is_numeric($_POST['courseid'])) {
                                    die("Invalid course ID");
                                }
                                $courseid = (int)$_POST['courseid'];
                                
                                // Main query with prepared statement
                                $sql1 = "SELECT
                                            u.id AS 'uid',
                                            c.id AS 'courseid',
                                            CONCAT(u.firstname, ' ', u.lastname) AS 'Participant Name',
                                            c.fullname AS 'Course Name',
                                            f.name AS 'Feedback Activity',
                                            i.name AS 'Question',
                                            v.value AS 'Response'
                                        FROM
                                            elp_feedback_completed fc
                                        JOIN
                                            elp_feedback f ON f.id = fc.feedback
                                        JOIN
                                            elp_course c ON c.id = ?
                                        JOIN
                                            elp_user u ON u.id = fc.userid
                                        JOIN
                                            elp_feedback_item i ON i.feedback = f.id
                                        JOIN
                                            elp_feedback_value v ON v.completed = fc.id AND v.item = i.id
                                        WHERE
                                            f.course != 1  -- Exclude site-level feedback
                                            AND fc.userid != 0
                                        GROUP BY u.id
                                        ORDER BY
                                            c.fullname, f.name, u.lastname, i.id";
                                
                                $stmt = $conn->prepare($sql1);
                                $stmt->bind_param("i", $courseid);
                                $stmt->execute();
                                $result1 = $stmt->get_result();
                                
                                // get feedback data 
                                $sql3 = "SELECT *
                                        FROM elp_feedback
                                        WHERE course = " . $courseid;
                                $result3 = $conn->query($sql3);
                                $row3 = $result3->fetch_assoc();

                                $feedback = $row3['id'];

                                $sql4 = "SELECT *
                                        FROM elp_feedback_item
                                        WHERE feedback = " . $row3['id'];
                                $result4 = $conn->query($sql4);

                                $numSoalan = 0;
                                $arrItem = [];
                                while ($row4 = $result4->fetch_assoc()) {
                                    if ($row4['name'] != '') {
                                        ++$numSoalan;
                                        $arrItem[] = $row4['id'];
                                    }
                                }

                                // ------------ get rubric info ----------------
                                $sqr1 = "select 
                                            gd.id AS definitionid
                                            -- CONCAT(u.firstname, ' ', u.lastname) AS student_name,
                                            -- c.fullname AS course,
                                            -- a.name AS assignment
                                        from elp_course as c
                                        join elp_assign a on a.course = c.id
                                        join elp_assign_grades ag on ag.assignment = a.id
                                        JOIN elp_user u ON u.id = ag.userid
                                        JOIN elp_grading_instances gi ON gi.itemid = ag.id
                                        JOIN elp_grading_definitions gd ON gd.id = gi.definitionid";
                                $rstr1 = $conn->query($sqr1);
                                $rowr1 = $rstr1->fetch_assoc();

                                $sqr2 = "select *
                                        from elp_gradingform_rubric_criteria";
                                $rstr2 = $conn->query($sqr2);

                                $numCriteria = $rstr2->num_rows;

                                $arrCriteria = [];
                                while ($rowr2 = $rstr2->fetch_assoc()){
                                    $arrCriteria[] = $rowr2['id'];
                                }
                    ?>
                    <div class="row">
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">List of available data</div>
                                <div class="card-body">
                                    <p class="card-title"></p>
                                    <table class="table table-hover" id="example1" width="100%">
                                        <thead>
                                            <tr>
                                                <th>POE / AGENCY</th>
                                                <th>COURSE NAME</th>
                                                <?php
                                                    for ($b=0; $b<$numSoalan; ) {
                                                ?>
                                                <th>QUESTION <?=++$b?></th>
                                                <?php } ?>
                                                <th>TOTAL MARK (%)</th>                                           
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php            
                                                $stmt = $conn->prepare($sql1);
                                                $stmt->bind_param("i", $courseid);
                                                $stmt->execute();
                                                $result1 = $stmt->get_result();

                                                while ($row1 = $result1->fetch_assoc()){
                                                    // get feedback_completed id 
                                                    $sf1 = "SELECT *
                                                            FROM elp_feedback_completed
                                                            WHERE feedback = " . $feedback . " AND userid = " . $row1['uid'];
                                                    $rstf1 = $conn->query($sf1);

                                                    $rwf1 = $rstf1->fetch_assoc();

                                                    //echo $sf1;                                                   

                                                    if ($rstf1->num_rows > 0) {
                                                        $complete = $rwf1['id'];
                                            ?>
                                            <tr>
                                                <td><?=$row1['Participant Name']?></td>
                                                <td><?=$row1['Course Name']?></td>
                                                <?php
                                                    $markah = 0;
                                                    $jumMarkah = 0;
                                                    for ($a=0; $a<$numSoalan; ++$a) {
                                                        $markah = getItemMark($conn, $arrItem[$a], $complete);
                                                        $jumMarkah += $markah;
                                                ?>
                                                <td><?=$markah?></td>
                                                <?php 
                                                    } 
                                                    $percentage = ($jumMarkah / 25) * 100;
                                                ?>
                                                <td><?=$percentage?></td>
                                                
                                            </tr>
                                            <?php
                                                    } // close if data available 
                                                } // tutup while row1 ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } // close filter ?>
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