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
        $title = "eLP Dashboard";

        require_once('includes/main-header.php');
        require_once('includes/connection.php');
        require_once('includes/functions.php');
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
                            <div class="page-pretitle">Overview</div>
                            <h2 class="page-title">Course Overview</h2>
                        </div>
                    </div>

                    <!-- Reporting 1 -->
                    <div class="row">
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">Available Courses</div>
                                <div class="card-body">
                                    <table class="table table-hover" id="dataTables-example1" width="100%">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>COURSE NAME</th>
                                            <th>TOTAL PARTICIPANTS</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $sql1 = "SELECT 
                                                            c.id,
                                                            c.fullname AS course_name,
                                                            c.shortname,
                                                            cat.name AS category,
                                                            COUNT(ue.id) AS enrolled_users,
                                                            FROM_UNIXTIME(c.startdate) AS start_date,
                                                            FROM_UNIXTIME(c.enddate) AS end_date
                                                        FROM 
                                                            elp_course c
                                                        JOIN 
                                                            elp_course_categories cat ON c.category = cat.id
                                                        LEFT JOIN 
                                                            elp_enrol e ON c.id = e.courseid
                                                        LEFT JOIN 
                                                            elp_user_enrolments ue ON e.id = ue.enrolid
                                                        WHERE 
                                                            c.visible = 1
                                                            AND (c.startdate <= UNIX_TIMESTAMP() OR c.startdate = 0)
                                                            AND (c.enddate >= UNIX_TIMESTAMP() OR c.enddate = 0)
                                                        GROUP BY 
                                                            c.id
                                                        ORDER BY 
                                                            cat.name, c.fullname";
                                                $rst1 = $conn->query($sql1);

                                                $num = 0;
                                                while ($row1 = $rst1->fetch_assoc()){
                                            ?>
                                            <tr>
                                                <td><?=++$num?></td>
                                                <td><?=strtoupper($row1['course_name'])?></td>
                                                <td><?=$row1['enrolled_users']?></td>
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
                pageLength: 10,
                lengthChange: false,
                searching: true,
                ordering: true
            });

            $('#dataTables-example2').DataTable({
                responsive: true,
                pageLength: 10,
                lengthChange: false,
                searching: true,
                ordering: true
            });
        })();
    </script>
</body>

</html>
