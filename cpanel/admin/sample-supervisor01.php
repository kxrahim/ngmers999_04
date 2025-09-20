<?php
require_once('../../config.php'); // Moodle DB connection config


$mysqli = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);
$mysqli->set_charset("utf8");

// Get selected course ID from form (0 means "All")
$selected_course_id = isset($_GET['courseid']) ? intval($_GET['courseid']) : 0;

// Get all courses for filter dropdown
$courses = $mysqli->query("SELECT id, fullname FROM elp_course ORDER BY fullname");

// Prepare main query
$sql = "
SELECT 
    u.id AS user_id,
    CONCAT(u.firstname, ' ', u.lastname) AS student_name,
    c.id AS course_id,
    c.fullname AS course_name,
    f.name AS feedback_name,
    fv.value AS comment,
    FROM_UNIXTIME(fc.timemodified) AS submitted_on
FROM elp_feedback f
JOIN elp_course c ON f.course = c.id
JOIN elp_feedback_completed fc ON fc.feedback = f.id
JOIN elp_user u ON u.id = fc.userid
JOIN elp_feedback_value fv ON fv.completed = fc.id
JOIN elp_feedback_item fi ON fi.id = fv.item AND fi.typ = 'textarea'
WHERE (? = 0 OR c.id = ?)
ORDER BY c.fullname, student_name
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $selected_course_id, $selected_course_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Moodle Feedback Report</title>
    <style>
        body { font-family: Arial; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background-color: #f4f4f4; }
        select { padding: 5px; }
    </style>
</head>
<body>

<h2>Student Feedback Comments</h2>

<form method="GET">
    <label for="courseid">Filter by Course:</label>
    <select name="courseid" id="courseid" onchange="this.form.submit()">
        <option value="0">-- All Courses --</option>
        <?php while ($course = $courses->fetch_assoc()): ?>
            <option value="<?= $course['id'] ?>" <?= $selected_course_id == $course['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($course['fullname']) ?>
            </option>
        <?php endwhile; ?>
    </select>
</form>

<table>
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Course</th>
            <th>Feedback Name</th>
            <th>Comment</th>
            <th>Submitted On</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['student_name']) ?></td>
                <td><?= htmlspecialchars($row['course_name']) ?></td>
                <td><?= htmlspecialchars($row['feedback_name']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['comment'])) ?></td>
                <td><?= $row['submitted_on'] ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No feedback found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
