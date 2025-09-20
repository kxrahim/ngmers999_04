<?php
// DB connection
require_once('../../config.php'); // Moodle DB connection config


$conn = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get course filter
$selectedCourse = isset($_GET['course']) ? $_GET['course'] : '';
$studentFilter = isset($_GET['student']) ? $_GET['student'] : '';

// 1. Get all unique questions
$questionQuery = "
    SELECT DISTINCT fb_item.id AS question_id, fb_item.name AS question_text
    FROM elp_feedback_item fb_item
    JOIN elp_feedback f ON fb_item.feedback = f.id
    JOIN elp_course c ON c.id = f.course
    WHERE 1=1";

$params = [];
$types = '';

if (!empty($selectedCourse)) {
    $questionQuery .= " AND c.id = ?";
    $params[] = $selectedCourse;
    $types .= 'i';
}

$questionQuery .= " ORDER BY fb_item.id";
$questionStmt = $conn->prepare($questionQuery);
if (!empty($types)) {
    $questionStmt->bind_param($types, ...$params);
}
$questionStmt->execute();
$questionResult = $questionStmt->get_result();

$questions = [];
while ($row = $questionResult->fetch_assoc()) {
    $questions[$row['question_id']] = $row['question_text'];
}
$questionStmt->close();

// 2. Get feedback responses
$sql = "
    SELECT 
        u.id AS user_id,
        CONCAT(u.firstname, ' ', u.lastname) AS student_name,
        c.fullname AS course_name,
        fb_item.id AS question_id,
        fb_value.value AS response
    FROM 
        elp_feedback f
    JOIN elp_feedback_item fb_item ON f.id = fb_item.feedback
    JOIN elp_feedback_completed fb_comp ON fb_comp.feedback = f.id
    JOIN elp_feedback_value fb_value ON fb_value.completed = fb_comp.id AND fb_value.item = fb_item.id
    JOIN elp_user u ON u.id = fb_comp.userid
    JOIN elp_course c ON c.id = f.course
    WHERE 1=1";

$types = '';
$params = [];

if (!empty($selectedCourse)) {
    $sql .= " AND c.id = ?";
    $types .= 'i';
    $params[] = $selectedCourse;
}
if (!empty($studentFilter)) {
    $sql .= " AND CONCAT(u.firstname, ' ', u.lastname) LIKE ?";
    $types .= 's';
    $params[] = "%$studentFilter%";
}

$sql .= " ORDER BY student_name, course_name, question_id";

$stmt = $conn->prepare($sql);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// 3. Organize data per student + course
$rows = [];
foreach ($result as $row) {
    $key = $row['user_id'] . '|' . $row['course_name'];
    if (!isset($rows[$key])) {
        $rows[$key] = [
            'student' => $row['student_name'],
            'course' => $row['course_name'],
            'responses' => [],
            'total' => 0
        ];
    }
    $response = is_numeric($row['response']) ? floatval($row['response']) : 0;
    $rows[$key]['responses'][$row['question_id']] = $response;
    $rows[$key]['total'] += $response;
}
$stmt->close();

// 4. Display course dropdown
echo "<form method='GET'>";
echo "Course: <select name='course'><option value=''>-- All Courses --</option>";
$courseList = $conn->query("SELECT id, fullname FROM elp_course ORDER BY fullname");
while ($course = $courseList->fetch_assoc()) {
    $selected = ($course['id'] == $selectedCourse) ? 'selected' : '';
    echo "<option value='{$course['id']}' $selected>" . htmlspecialchars($course['fullname']) . "</option>";
}
echo "</select> ";

echo "Student: <input type='text' name='student' value='" . htmlspecialchars($studentFilter) . "'> ";
echo "<input type='submit' value='Filter'>";
echo "</form><br>";

// 5. Display pivot table
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Student</th><th>Course</th>";
foreach ($questions as $qid => $qtext) {
    echo "<th>" . htmlspecialchars($qtext) . "</th>";
}
echo "<th>Total</th></tr>";

foreach ($rows as $row) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['student']) . "</td>";
    echo "<td>" . htmlspecialchars($row['course']) . "</td>";
    foreach ($questions as $qid => $_) {
        $val = isset($row['responses'][$qid]) ? $row['responses'][$qid] : '';
        echo "<td>" . htmlspecialchars($val) . "</td>";
    }
    echo "<td><strong>" . $row['total'] . "</strong></td>";
    echo "</tr>";
}

echo "</table>";

$conn->close();
?>