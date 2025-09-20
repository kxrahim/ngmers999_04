<?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('session.save_path', '/var/lib/php/sessions');
?>
<?php
    //include("includes/check.php");
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
                <?php
                    if ($_SESSION['role'] == 1){
                ?>
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 page-header">
                            <div class="page-pretitle">Overview</div>
                            <h2 class="page-title">Dashboard</h2>
                        </div>
                    </div>
                    <!--
                    <div class="row">
                        <div class="col-sm-6 col-md-6 col-lg-3 mt-3">
                            <div class="card">
                                <div class="content">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="icon-big text-center">
                                                <i class="teal fas fa-shopping-cart"></i>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="detail">
                                                <p class="detail-subtitle">New Orders</p>
                                                <span class="number">6,267</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="footer">
                                        <hr />
                                        <div class="stats">
                                            <i class="fas fa-calendar"></i> For this Week
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-3 mt-3">
                            <div class="card">
                                <div class="content">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="icon-big text-center">
                                                <i class="olive fas fa-money-bill-alt"></i>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="detail">
                                                <p class="detail-subtitle">Revenue</p>
                                                <span class="number">$180,900</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="footer">
                                        <hr />
                                        <div class="stats">
                                            <i class="fas fa-calendar"></i> For this Month
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-3 mt-3">
                            <div class="card">
                                <div class="content">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="icon-big text-center">
                                                <i class="violet fas fa-eye"></i>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="detail">
                                                <p class="detail-subtitle">Page views</p>
                                                <span class="number">28,210</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="footer">
                                        <hr />
                                        <div class="stats">
                                            <i class="fas fa-stopwatch"></i> For this Month
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-3 mt-3">
                            <div class="card">
                                <div class="content">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="icon-big text-center">
                                                <i class="orange fas fa-envelope"></i>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="detail">
                                                <p class="detail-subtitle">Support Request</p>
                                                <span class="number">75</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="footer">
                                        <hr />
                                        <div class="stats">
                                            <i class="fas fa-envelope-open-text"></i> For this week
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    -->

                    <!-- activities -->
                    <div class="row">
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">Assessment</div>
                                <div class="card-body">
                                    <p class="card-title"></p>
                                    <table class="table table-hover table-striped table-responsive" id="dataTables-example1" width="100%">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>DATE</th>
                                            <th>OBSERVATION</th>
                                            <th>STUDENT</th>
                                            <th>ASSESSOR</th>
                                            <th>STATUS</th>
                                            <th></th>
                                            <!--
                                            <th></th>
                                            -->
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $sq2 = "SELECT a.feedback_id, a.id as assId, d.observation as observation, b.fullname AS assessorName, e.fullname AS studentName, a.bookingDate AS date, a.bookingTime AS time
                                                FROM assessments AS a 
                                                JOIN users AS b ON a.assessor_id = b.id
                                                JOIN activities AS c ON a.activity_id = c.id
                                                JOIN observations AS d ON a.observation_id = d.id
                                                JOIN users AS e ON a.user_id = e.id
                                                WHERE a.status = 1
                                                ORDER BY date DESC";
                                        $rs2 = $conn->query($sq2);

                                        $num = 0;

                                        $feedbackStatus = '';
                                        $discussionStatus = 0;
                                        while ($rw2 = $rs2->fetch_assoc()){
                                            //$aID = $rw1['id'];
                                            if ($rw2['feedback_id'] > 0){
                                                $discussionStatus = 1;
                                                $feedbackStatus = 'Completed';
                                                $feedbackClass = 'text-success';

                                            } else {
                                                $discussionStatus = 0;
                                                $feedbackStatus = 'In-completed';
                                                $feedbackClass = 'text-warning';

                                            }

                                        ?>
                                            <tr>
                                                <td><?=++$num;?></td>
                                                <td style="width: 90px;"><?=date('d-m-Y', $rw2['date']);?></td>
                                                <td><?=$rw2['observation'];?></td>
                                                <td><?=$rw2['studentName'];?></td>
                                                <td><?=$rw2['assessorName'];?></td>
                                                <td class="<?=$feedbackClass;?>"><?=$feedbackStatus;?></td>
                                                <td>
                                                    <a href="session-details.php?id=<?=$rw2['assId'];?>" class="btn btn-primary btn-sm mb-1 w-100" target="_blank"><i class="fa fa-search"></i> Detail</a>
                                                    <?php
                                                        if ($discussionStatus > 0){
                                                    ?>
                                                    <a href="#" class="btn btn-secondary btn-sm w-100" target="_blank"><i class="fa fa-comment"></i> Discussion</a>
                                                    <?php
                                                        } else {
                                                        }
                                                    ?>

                                                </td>

                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- sessions and assessment -->
                    <div class="row">
                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">Activities</div>
                                <div class="card-body">
                                    <p class="card-title"></p>
                                    <table class="table table-hover" id="dataTables-example" width="100%">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ACTIVITY</th>
                                            <th># SESSION</th>
                                            <!--
                                            <th></th>
                                            -->
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $sq1 = "select *
                                                        from activities
                                                        where status = '1'";
                                        $rs1 = $conn->query($sq1);

                                        $num = 0;
                                        while ($rw1 = $rs1->fetch_assoc()){
                                            $aID = $rw1['id'];

                                            if ($rw1['status'] == '1'){
                                                $status = 'Active';
                                            } else {
                                                $status = 'Inactive';
                                            }
                                            ?>
                                            <tr>
                                                <td><?=++$num;?></td>
                                                <td><?=$rw1['activity'];?></td>
                                                <td><?=getActivityTotal($conn, $aID);?></td>
                                                <!--
                                                <td>
                                                    <a href="?n=1&id=<?=$rw1['id'];?>" title="Update/Edit"><i class="fa fa-edit"></i></a>
                                                    <a href="?n=3&id=<?=$rw1['id'];?>" title="Delete"><i class="fa fa-trash" onClick="javascript:return confirm('Are you sure to delete this?');"></i></a>
                                                </td>
                                                -->
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="content">
                                            <div class="head">
                                                <h5 class="mb-0">Traffic Overview</h5>
                                                <p class="text-muted">Current year website visitor data</p>
                                            </div>
                                            <div class="canvas-wrapper">
                                                <canvas class="chart" id="trafficflow"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="content">
                                            <div class="head">
                                                <h5 class="mb-0">Sales Overview</h5>
                                                <p class="text-muted">Current year sales data</p>
                                            </div>
                                            <div class="canvas-wrapper">
                                                <canvas class="chart" id="sales"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="content">
                                    <div class="head">
                                        <h5 class="mb-0">Top Visitors by Country</h5>
                                        <p class="text-muted">Current year website visitor data</p>
                                    </div>
                                    <div class="canvas-wrapper">
                                        <table class="table table-striped">
                                            <thead class="success">
                                            <tr>
                                                <th>Country</th>
                                                <th class="text-end">Unique Visitors</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td><i class="flag-icon flag-icon-us"></i> United States</td>
                                                <td class="text-end">27,340</td>
                                            </tr>
                                            <tr>
                                                <td><i class="flag-icon flag-icon-in"></i> India</td>
                                                <td class="text-end">21,280</td>
                                            </tr>
                                            <tr>
                                                <td><i class="flag-icon flag-icon-jp"></i> Japan</td>
                                                <td class="text-end">18,210</td>
                                            </tr>
                                            <tr>
                                                <td><i class="flag-icon flag-icon-gb"></i> United Kingdom</td>
                                                <td class="text-end">15,176</td>
                                            </tr>
                                            <tr>
                                                <td><i class="flag-icon flag-icon-es"></i> Spain</td>
                                                <td class="text-end">14,276</td>
                                            </tr>
                                            <tr>
                                                <td><i class="flag-icon flag-icon-de"></i> Germany</td>
                                                <td class="text-end">13,176</td>
                                            </tr>
                                            <tr>
                                                <td><i class="flag-icon flag-icon-br"></i> Brazil</td>
                                                <td class="text-end">12,176</td>
                                            </tr>
                                            <tr>
                                                <td><i class="flag-icon flag-icon-id"></i> Indonesia</td>
                                                <td class="text-end">11,886</td>
                                            </tr>
                                            <tr>
                                                <td><i class="flag-icon flag-icon-ph"></i> Philippines</td>
                                                <td class="text-end">11,509</td>
                                            </tr>
                                            <tr>
                                                <td><i class="flag-icon flag-icon-nz"></i> New Zealand</td>
                                                <td class="text-end">1,700</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="content">
                                    <div class="head">
                                        <h5 class="mb-0">Most Visited Pages</h5>
                                        <p class="text-muted">Current year website visitor data</p>
                                    </div>
                                    <div class="canvas-wrapper">
                                        <table class="table table-striped">
                                            <thead class="success">
                                            <tr>
                                                <th>Page Name</th>
                                                <th class="text-end">Visitors</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>/about.html <a href="#"><i class="fas fa-link blue"></i></a></td>
                                                <td class="text-end">8,340</td>
                                            </tr>
                                            <tr>
                                                <td>/special-promo.html <a href="#"><i class="fas fa-link blue"></i></a></td>
                                                <td class="text-end">7,280</td>
                                            </tr>
                                            <tr>
                                                <td>/products.html <a href="#"><i class="fas fa-link blue"></i></a></td>
                                                <td class="text-end">6,210</td>
                                            </tr>
                                            <tr>
                                                <td>/documentation.html <a href="#"><i class="fas fa-link blue"></i></a></td>
                                                <td class="text-end">5,176</td>
                                            </tr>
                                            <tr>
                                                <td>/customer-support.html <a href="#"><i class="fas fa-link blue"></i></a></td>
                                                <td class="text-end">4,276</td>
                                            </tr>
                                            <tr>
                                                <td>/index.html <a href="#"><i class="fas fa-link blue"></i></a></td>
                                                <td class="text-end">3,176</td>
                                            </tr>
                                            <tr>
                                                <td>/products-pricing.html <a href="#"><i class="fas fa-link blue"></i></a></td>
                                                <td class="text-end">2,176</td>
                                            </tr>
                                            <tr>
                                                <td>/product-features.html <a href="#"><i class="fas fa-link blue"></i></a></td>
                                                <td class="text-end">1,886</td>
                                            </tr>
                                            <tr>
                                                <td>/contact-us.html <a href="#"><i class="fas fa-link blue"></i></a></td>
                                                <td class="text-end">1,509</td>
                                            </tr>
                                            <tr>
                                                <td>/terms-and-condition.html <a href="#"><i class="fas fa-link blue"></i></a></td>
                                                <td class="text-end">1,100</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <?php
                    } else if ($_SESSION['role'] == 2){
                        echo 'ini dia';
                    } else {

                    }
                ?>
            </div>
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
        })();
    </script>
</body>

</html>