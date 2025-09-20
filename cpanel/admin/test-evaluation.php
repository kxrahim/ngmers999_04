<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <?php
    require_once('includes/connection.php');
    require_once('includes/functions.php');
  ?> 

</head>
<body>

<div class="container mt-3">
  <h2>Striped Rows</h2>
  <p>The .table-striped class adds zebra-stripes to a table:</p>      
  
  <div class="row">
    <form action="" method="post">
        <div class="group-form">
            <label for="" class="form-label">Select Course</label>
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
                <option value="<?=$row2['Course ID']?>"> <?=$row2['Course Name']?> [<?=$row2['Course ID']?>] </option>
                <?php } // close while ?>
            </select>

            <button class="btn btn-info" type="submit" name="btnFilter"> 
                Filter Feedback
            </button>
        </div>
        
    </form>
  </div>

  <!-- ------------------- Process to display ------------------------ -->


  <?php
    if (isset($_POST['btnFilter'])) {
        $courseid = $_POST['courseid'];
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
                    elp_course c ON c.id = '$courseid'
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
        //$numSoalan = $result4->num_rows;

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

        $sqr2 = 'select *
                 from elp_gradingform_rubric_criteria
                 where definitionid = ' . $rowr1['definitionid'];
        $rstr2 = $conn->query($sqr2);

        $numCriteria = $rstr2->num_rows;

        $arrCriteria = [];
        while ($rowr2 = $rstr2->fetch_assoc()){
            $arrCriteria[] = $rowr2['id'];
        }

  ?>

  <div class="row">
    <table class="table table-striped" style="font-size: 11px;">
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
        <th>STAFF EVALUATION (%)</th>
        <?php
            for ($c=0; $c<$numCriteria; ) {
        ?>
        <th>SUPERVISOR <?=++$c?></th>
        <?php } ?>
        <th>SUPERVISOR EVALUATION (%)</th>
      </tr>
    </thead>
        <tbody>
        <?php            
            $result1 = $conn->query($sql1);

            while ($row1 = $result1->fetch_assoc()){
                // get feedback_completed id 
                $sf1 = "SELECT *
                        FROM elp_feedback_completed
                        WHERE feedback = " . $feedback . " AND userid = " . $row1['uid'];
                $rstf1 = $conn->query($sf1);

                $rwf1 = $rstf1->fetch_assoc();

                //echo $sf1;

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
            <?php
                // get assignment mark
                $sf2 = "SELECT 
                            c.id AS course_id,
                            c.shortname AS course_shortname,
                            c.fullname AS course_name,
                            a.id AS assignment_id,
                            a.name AS assignment_name,
                            a.grade AS max_grade,
                            u.id AS student_id,
                            u.username,
                            CONCAT(u.firstname, ' ', u.lastname) AS student_name,
                            ag.grade AS final_grade,
                            FROM_UNIXTIME(ag.timemodified) AS graded_date,
                            CONCAT(grader.firstname, ' ', grader.lastname) AS graded_by,
                            -- Feedback information
                            af.commenttext AS feedback_comments,
                            -- Submission information
                            asub.status AS submission_status,
                            FROM_UNIXTIME(asub.timemodified) AS submission_date,
                            -- Grade breakdown (if using simple direct grading)
                            ag.grade AS awarded_grade,
                            (ag.grade/a.grade)*100 AS percentage
                        FROM 
                            elp_assign_grades ag
                        JOIN 
                            elp_assign a ON ag.assignment = a.id
                        JOIN 
                            elp_course_modules cm ON a.id = cm.instance
                        JOIN 
                            elp_course c ON c.id = '$courseid'
                        JOIN 
                            elp_user u ON u.id = " . $row1['uid'] . "
                        JOIN 
                            elp_user grader ON ag.grader = grader.id
                        JOIN 
                            elp_modules m ON cm.module = m.id
                        LEFT JOIN 
                            elp_assignfeedback_comments af ON (ag.id = af.grade AND af.assignment = a.id)
                        LEFT JOIN 
                            elp_assign_submission asub ON (a.id = asub.assignment AND u.id = asub.userid AND asub.latest = 1)
                        WHERE 
                            m.name = 'assign'
                        ORDER BY 
                            c.shortname, a.name, u.lastname, u.firstname";

                //echo $sf2 . '<hr>';

                $rstf2 = $conn->query($sf2);
                $rwf2 = $rstf2->fetch_assoc();                
            ?>
            <td><?=$rwf2['awarded_grade']?></td>
            <td>
                <?=print_r($arrCriteria)?>
            </td>
        </tr>
        <?php } // tutup while row1 ?>
        </tbody>
    </table>
  </div>
  
  <?php } // --- close btnFilter  ?>
</div>


</body>
</html>


<!-- select 
      gd.id AS 'GI ID'
      -- CONCAT(u.firstname, ' ', u.lastname) AS student_name,
      -- c.fullname AS course,
      -- a.name AS assignment
from elp_course as c
join elp_assign a on a.course = c.id
join elp_assign_grades ag on ag.assignment = a.id
JOIN elp_user u ON u.id = ag.userid
JOIN elp_grading_instances gi ON gi.itemid = ag.id
JOIN elp_grading_definitions gd ON gd.id = gi.definitionid -->