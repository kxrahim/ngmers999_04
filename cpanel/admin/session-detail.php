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
                            <div class="page-pretitle">Report</div>
                            <h2 class="page-title">Activity</h2>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">Filter</div>
                                <div class="card-body">
                                    <form action="" method="get">
                                        <div class="row">
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
                                                <?php
                                                    $sql01 = "select *
                                                              from activities
                                                              order by id asc";
                                                    $rst01 = $conn->query($sql01);
                                                    while ($row01 = $rst01->fetch_assoc()){
                                                ?>
                                                <option value="<?=$row01['id'];?>"><?=$row01['activity'];?></option>
                                                <?php
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <button class="btn btn-primary" type="submit" name="filter"> Filter </button>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12 col-lg-12 mt-3">
                            <div class="card">
                                <div class="card-header">List of available activities</div>
                                <div class="card-body">
                                    <p class="card-title"></p>
                                    <table class="table table-hover table-striped" id="dataTables-example" width="100%">
                                        <thead>
                                            <tr>
                                                <th>ACTIVITY</th>
                                                <th>TOTAL</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $sq1 = "SELECT * 
                                                        from activities
                                                        order by id asc";
                                                $rs1 = $conn->query($sq1);

                                               // echo $sq1;

                                                $num = 0;
                                                while ($rw1 = $rs1->fetch_assoc()){

                                            ?>
                                            <tr>
                                                <td><?=$rw1['activity'];?></td>
                                                <td><?=getActivityTotal ($conn, $rw1['id']);?></td>
                                                <td></td>
                                                <!--
                                                <td>
                                                    <a href="?n=1&id=<?=$rw1['oid'];?>" title="Update/Edit"><i class="fa fa-edit"></i></a>
                                                    <a href="?n=3&id=<?=$rw1['oid'];?>" title="Delete"><i class="fa fa-trash" onClick="javascript:return confirm('Are you sure to delete this?');"></i></a>
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