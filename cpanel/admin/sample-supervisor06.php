<?php
// DB connection
require_once('../../config.php'); // Moodle DB connection config


$conn = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get selected course
$selectedCourse = isset($_GET['course']) ? $_GET['course'] : '';

// Get all courses for dropdown
$courseOptions = [];
$courseRes = $conn->query("SELECT id, fullname FROM elp_course ORDER BY fullname");
while ($row = $courseRes->fetch_assoc()) {
    $courseOptions[$row['id']] = $row['fullname'];
}

// Prepare dropdown form
echo "<form method='GET'>";
echo "Course: <select name='course'>";
echo "<option value=''>-- Select Course --</option>";
foreach ($courseOptions as $id => $name) {
    $selected = ($id == $selectedCourse) ? "selected" : "";
    echo "<option value='$id' $selected>" . htmlspecialchars($name) . "</option>";
}
echo "</select> ";
echo "<input type='submit' value='Show'>";
echo "</form><br>";

// Stop here if no course selected
if (empty($selectedCourse)) {
    echo "<p>Please select a course to display data.</p>";
    exit;
}

// Get all questions for the selected course
$questionSql = "
    SELECT DISTINCT fb_item.id, fb_item.name
    FROM elp_feedback f
    JOIN elp_feedback_item fb_item ON f.id = fb_item.feedback
    WHERE f.course = ?
    ORDER BY fb_item.id
";

$qStmt = $conn->prepare($questionSql);
$qStmt->bind_param('i', $selectedCourse);
$qStmt->execute();
$questionResult = $qStmt->get_result();

$questions = [];
while ($q = $questionResult->fetch_assoc()) {
    $questions[$q['id']] = $q['name'];
}
$qStmt->close();

if (empty($questions)) {
    echo "<p>No questions found for this course.</p>";
    exit;
}

// Main data query
$dataSql = "
    SELECT 
        u.id AS user_id,
        CONCAT(u.firstname, ' ', u.lastname) AS student,
        c.fullname AS course,
        fb_item.id AS question_id,
        fb_value.value AS response
    FROM 
        elp_feedback f
    JOIN elp_feedback_item fb_item ON f.id = fb_item.feedback
    JOIN elp_feedback_completed fb_comp ON fb_comp.feedback = f.id
    JOIN elp_feedback_value fb_value ON fb_value.completed = fb_comp.id AND fb_value.item = fb_item.id
    JOIN elp_user u ON u.id = fb_comp.userid
    JOIN elp_course c ON c.id = f.course
    WHERE c.id = ?
";

$stmt = $conn->prepare($dataSql);
$stmt->bind_param('i', $selectedCourse);
$stmt->execute();
$result = $stmt->get_result();

// Pivot data by user
$students = [];
while ($row = $result->fetch_assoc()) {
    $uid = $row['user_id'];
    if (!isset($students[$uid])) {
        $students[$uid] = [
            'student' => $row['student'],
            'course' => $row['course'],
            'marks' => [],
            'total' => 0,
        ];
    }
    $response = is_numeric($row['response']) ? floatval($row['response']) : 0;
    $students[$uid]['marks'][$row['question_id']] = $response;
    $students[$uid]['total'] += $response;
}
$stmt->close();

// Display table
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Student</th><th>Course</th>";
foreach ($questions as $qid => $qname) {
    echo "<th>" . htmlspecialchars($qname) . "</th>";
}
echo "<th>Total</th></tr>";

foreach ($students as $student) {
    echo "<tr>";
    echo "<td>{$student['student']}</td>";
    echo "<td>{$student['course']}</td>";
    foreach ($questions as $qid => $qname) {
        $mark = isset($student['marks'][$qid]) ? $student['marks'][$qid] : '-';
        echo "<td>$mark</td>";
    }
    echo "<td>{$student['total']}</td>";
    echo "</tr>";
}

echo "</table>";
$conn->close();
?>