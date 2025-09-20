<?php
// DB connection
require_once('../../config.php'); // Moodle DB connection config


$conn = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filters from GET request
$courseFilter = isset($_GET['course']) ? $_GET['course'] : null;
$studentFilter = isset($_GET['student']) ? $_GET['student'] : null;

// Prepare SQL
$sql = "
    SELECT 
        c.id AS course_id,
        c.fullname AS course_name,
        f.id AS feedback_id,
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

if (!empty($courseFilter)) {
    $sql .= " AND c.fullname LIKE ?";
}
if (!empty($studentFilter)) {
    $sql .= " AND CONCAT(u.firstname, ' ', u.lastname) LIKE ?";
}

$sql .= " ORDER BY c.fullname, f.name, u.lastname";

// Prepare and bind
$stmt = $conn->prepare($sql);

$types = '';
$params = [];

if (!empty($courseFilter)) {
    $types .= 's';
    $params[] = "%$courseFilter%";
}
if (!empty($studentFilter)) {
    $types .= 's';
    $params[] = "%$studentFilter%";
}

if ($types) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Store data by user
$data = [];
while ($row = $result->fetch_assoc()) {
    $userId = $row['user_id'];
    $data[$userId]['username'] = $row['username'];
    $data[$userId]['course'] = $row['course_name'];
    $data[$userId]['feedback'][] = [
        'question' => $row['question_text'],
        'response' => $row['response']
    ];
}

// Output HTML
echo "<form method='GET'>";
echo "Filter by Course: <input type='text' name='course' value='" . htmlspecialchars($courseFilter) . "'> ";
echo "Filter by Student: <input type='text' name='student' value='" . htmlspecialchars($studentFilter) . "'> ";
echo "<input type='submit' value='Filter'>";
echo "</form>";

foreach ($data as $student) {
    echo "<h3>Student: {$student['username']} (Course: {$student['course']})</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Question</th><th>Response</th></tr>";
    $total = 0;
    foreach ($student['feedback'] as $item) {
        echo "<tr><td>{$item['question']}</td><td>{$item['response']}</td></tr>";
        $total += is_numeric($item['response']) ? $item['response'] : 0;
    }
    echo "<tr><td><strong>Total</strong></td><td><strong>$total</strong></td></tr>";
    echo "</table><br>";
}

$stmt->close();
$conn->close();
?>
