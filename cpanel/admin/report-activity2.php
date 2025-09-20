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
        require_once('includes/connection_old.php');
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>


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

                    <!-- display statistic -->
                    <div class="col-md-12 col-lg-12 mt-3">
                        <div class="card">
                            <div class="card-header">Statistic</div>
                            <div class="card-body">
                                <p class="card-title"></p>
                                <div class="canvas-wrapper">
                                    <canvas class="chart" id="myChart" width="602" height="180" style="display: block; box-sizing: border-box; height: 602px; width: 602px;"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
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
                                                <td><a href="report-activity-details.php?id=<?=$rw1['id'];?>"><?=$rw1['activity'];?></a></td>
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

    <?php
        $sq1 = "SELECT *
                from activities
                order by id asc";
        $rs1 = $conn->query($sq1);

        $num = 0;

        $data = '';
        while ($rw1 = $rs1->fetch_assoc()){
            $nilai = getActivityTotal ($conn, $rw1['id']);

            $data .= $nilai . ',';
        }

        echo $data;
    ?>


    <script>
        var xValues = ["Formulating and communicating an appropriate treatment plan for a patient with dental caries",
        "Formulating and communicating an appropriate treatment plan for a patient with malocclusion",
        "Formulating and communicating an appropriate treatment plan for a patient with periodontals disease.",
        "Managing care of a patient with gingivitis.",
        "Managing care of a patient with pulpal disease in a permanent tooth.",
        "Managing care of a patient with pulpal disease in a primary/young permanent tooth.",
        "Managing care of an adult patient with a periodontally non-salvageable tooth/non-restorable tooth.",
        "Managing care of an adult patient with partial tooth loss.",
        "Managing the preventive care of a patient with initial dental caries.",
        "Managing the restorative care of a patient with moderate to extensive dental caries."];
        // var yValues = [55, 49, 44, 24, 15];
        var yValues = [<?php echo $data; ?>];
        var barColors = [
        "#b91d47",
        "#00aba9",
        "#2b5797",
        "#e8c3b9",
        "#1e7145",
        "#CD6155",
        "#9B59B6",
        "#2980B9",
        "#82E0AA",
        "#F1C40F",
        "#D35400"
        ];

        new Chart("myChart", {
            type: "doughnut",
            data: {
                labels: xValues,
                datasets: [{
                backgroundColor: barColors,
                data: yValues
                }]
        },
        options: {
            legend: {
                display: true,
                position: 'right',
            },
            title: {
            display: true,
            text: "Statistic of Active Activity Created by Students"
            },

        }
        });
    </script>


</body>

</html>
