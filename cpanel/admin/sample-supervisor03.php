<?php
// DB connection
require_once('../../config.php'); // Moodle DB connection config


$conn = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filters from GET parameters
$courseFilter = isset($_GET['course']) ? $_GET['course'] : null;
$studentFilter = isset($_GET['student']) ? $_GET['student'] : null;

// Build SQL query
$sql = "
    SELECT 
        c.fullname AS course_name,
        f.name AS feedback_name,
        fb_item.name AS question_text,
        fb_value.value AS response,
        u.id AS user_id,
        CONCAT(u.firstname, ' ', u.lastname) AS username
    FROM 
        elp_feedback f
    JOIN elp_feedback_item fb_item ON f.id = fb_item.feedback
    JOIN elp_feedback_completed fb_comp ON fb_comp.feedback = f.id
    JOIN elp_feedback_value fb_value ON fb_value.completed = fb_comp.id AND fb_value.item = fb_item.id
    JOIN elp_user u ON u.id = fb_comp.userid
    JOIN elp_course c ON c.id = f.course
    WHERE 1=1
";

$types = '';
$params = [];

if (!empty($courseFilter)) {
    $sql .= " AND c.fullname LIKE ?";
    $types .= 's';
    $params[] = "%$courseFilter%";
}
if (!empty($studentFilter)) {
    $sql .= " AND CONCAT(u.firstname, ' ', u.lastname) LIKE ?";
    $types .= 's';
    $params[] = "%$studentFilter%";
}

$sql .= " ORDER BY username, course_name, feedback_name";

// Prepare and execute
$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Group responses for total marks
$totals = [];
$data = [];

while ($row = $result->fetch_assoc()) {
    $uid = $row['user_id'];
    $response = is_numeric($row['response']) ? floatval($row['response']) : 0;
    $totals[$uid]['total'] = ($totals[$uid]['total'] ?? 0) + $response;
    $totals[$uid]['username'] = $row['username'];

    $data[] = array_merge($row, ['response_numeric' => $response]);
}

// Display filter form
echo "<form method='GET'>";
echo "Course: <input type='text' name='course' value='" . htmlspecialchars($courseFilter) . "'> ";
echo "Student: <input type='text' name='student' value='" . htmlspecialchars($studentFilter) . "'> ";
echo "<input type='submit' value='Filter'>";
echo "</form><br>";

// Display single table
echo "<table border='1' cellpadding='5'>";
echo "<tr>
        <th>Student</th>
        <th>Course</th>
        <th>Feedback Question</th>
        <th>Response</th>
        <th>Total Mark</th>
      </tr>";

$lastUser = null;

foreach ($data as $row) {
    $uid = $row['user_id'];
    echo "<tr>";
    echo "<td>" . ($lastUser != $uid ? $row['username'] : "") . "</td>";
    echo "<td>" . $row['course_name'] . "</td>";
    echo "<td>" . $row['question_text'] . "</td>";
    echo "<td>" . $row['response'] . "</td>";
    echo "<td>" . ($lastUser != $uid ? $totals[$uid]['total'] : "") . "</td>";
    echo "</tr>";
    $lastUser = $uid;
}

echo "</table>";

$stmt->close();
$conn->close();
?>