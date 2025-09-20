<?php
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <?php
        require_once('includes/connection_old.php');
        require_once('includes/main-header.php');
    ?>
</head>
<body>
    <!-- papar senarai aktiviti -->

    <table class="table table-bordered">
    <?php
        $sid = 18;
        $sc1 = "SELECT *
                FROM activities
                WHERE status = 1
                ORDER BY id ASC";
        $rsc1 = $conn->query($sc1);
        while ($rcw1 = $rsc1->fetch_assoc()){
    ?>
        <tr>
            <th style="background-color:#000000; color:aliceblue;"><?=$rcw1['activity'];?></th>
        </tr>
        <tr>
            <td>
            <!-- paparkan senarai observation -->
            <table>
                <?php
                    $sc2 = "SELECT *
                            FROM observations
                            WHERE activity_id = " . $rcw1['id'] . "
                                  AND status = 1";
                    $rsc2 = $conn->query($sc2);
                    while ($rcw2 = $rsc2->fetch_assoc()){
                ?>
                <tr>
                    <th><?=$rcw2['observation'];?></th>
                </tr>
                <tr>
                    <td>
                        <table class="table table-bordered">
                            <tr>
                                <th>Date Completed</th>
                                <th>Assessor</th>
                                <th>Outcome</th>
                                <th>Domain 1</th>
                                <th>Domain 2</th>
                                <th>Domain 3</th>
                                <th>Domain 4</th>
                                <th>Domain 5</th>
                                <th>Outcome</th>
                                <th>Duration</th>
                            </tr>
                            <?php
                                $sc3 = "SELECT *
                                        FROM assessments AS a
                                        JOIN assessment_assessors AS b ON a.id = b.assessment_id
                                        WHERE a.user_id = 18
                                        AND a.activity_id = " . $rcw1['id'] . "
                                        AND a.observation_id = " . $rcw2['id'] . "
                                        AND a.reflection_id > 0";
                                //echo $sc3;
                                $rsc3 = $conn->query($sc3);
                                if ($rsc3->num_rows > 0){
                                    while ($rcw3 = $rsc3->fetch_assoc()){
                            ?>
                            <tr>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                            <?php
                                    }

                                } else {
                            ?>
                            <tr>
                                <td colspan="10"> No assessment Completed</td>
                            </tr>
                            <?php
                                }
                            ?>
                        </table>
                    </td>
                </tr>
                <?php
                    }
                ?>
            </table>
            </td>
        </tr>
    <?php
        }
    ?>
    </table>
</body>
</html>
