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
                            <h2 class="page-title">Training Evaluation - Team Leader</h2>
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
                                                    <option value="<?=$row2['Course ID']?>"> <?=$row2['Course Name']?></option>
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

                                $sql1 = "SELECT 
                                            a.id AS assignment_id,
                                            a.name AS assignment_name,
                                            c.id AS course_id,
                                            c.fullname AS course_name,
                                            GROUP_CONCAT(DISTINCT t.name ORDER BY t.name SEPARATOR ', ') AS tags
                                        FROM 
                                            elp_assign a
                                        JOIN 
                                            elp_course_modules cm ON a.id = cm.instance
                                        JOIN 
                                            elp_modules m ON cm.module = m.id
                                        JOIN 
                                            elp_course c ON cm.course = c.id
                                        LEFT JOIN 
                                            elp_tag_instance ti ON ti.itemid = cm.id AND ti.itemtype = 'course_modules'
                                        LEFT JOIN 
                                            elp_tag t ON ti.tagid = t.id
                                        WHERE 
                                            m.name = 'assign'
                                            AND c.id = '$courseid'
                                        GROUP BY 
                                            a.id, a.name, c.id, c.fullname
                                        ORDER BY 
                                            c.fullname, a.name";
                                $rst1 = $conn->query($sql1);
                                $row1 = $rst1->fetch_assoc();

                                $user_tags = $row1['tags'] ?? null;

                                //echo $sql1;
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
                                                <th>GRADE (%)</th>     
                                                <th>FEEDBACK / COMMENT</th>                                      
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php

                                            if ($user_tags == 'supervisor_report') {
                                            //while ($row1 = $rst1->fetch_assoc()){
                                                $aid = $row1['assignment_id'];
                                                $sql2 = "SELECT 
                                                            a.id AS assignment_id,
                                                            a.name AS assignment_name,
                                                            u.id AS user_id,
                                                            u.firstname AS firstname,
                                                            u.lastname AS lastname,
                                                            ag.grade AS grade_received,
                                                            a.grade AS max_grade,
                                                            ag.timemodified AS grade_time,
                                                            ag.id AS grade_id
                                                        FROM 
                                                            elp_assign a
                                                        JOIN 
                                                            elp_assign_grades ag ON a.id = ag.assignment
                                                        JOIN 
                                                            elp_user u ON ag.userid = u.id
                                                        WHERE 
                                                            a.id = '$aid'
                                                        ORDER BY 
                                                            u.lastname, u.firstname";
                                                //echo $sql2;
                                                $rst2 = $conn->query($sql2);
                                                if ($rst2->num_rows > 0){
                                                    while ($row2 = $rst2->fetch_assoc()){
                                                        if ($row2['grade_received'] > 0){
                                        ?>
                                            <tr>
                                                <td><?=strtoupper($row2['firstname']) . ' ' . strtoupper($row2['lastname']) ?></td>
                                                <td><?=$row1['course_name']?></td>
                                                <td><?=sprintf('%.2f',$row2['grade_received'])?></td>
                                                <td><?=get_assignfeedback_comments($conn, $row2['grade_id']) ?></td>
                                            </tr>
                                        <?php
                                                        } // close if > 0
                                                    }
                                                } else {
                                                    //echo "<tr><td colspan='4'>No record found</td></tr>";    
                                                }
                                                
                                        ?>
                                        
                                        <?php
                                            } // close check if supervisor_report
                                        ?>
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