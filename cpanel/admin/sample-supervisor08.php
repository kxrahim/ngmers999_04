<?php
// DB connection
require_once('../../config.php'); // Moodle DB connection config


$conn = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filters
$selectedCourse = isset($_GET['course']) ? $_GET['course'] : '';
$studentFilter = isset($_GET['student']) ? $_GET['student'] : '';

// Get all courses
$courseOptions = [];
$res = $conn->query("SELECT id, fullname FROM elp_course ORDER BY fullname");
while ($row = $res->fetch_assoc()) {
    $courseOptions[$row['id']] = $row['fullname'];
}

// Display filter form
echo "<form method='GET'>";
echo "Course: <select name='course'>";
echo "<option value=''>-- Select Course --</option>";
foreach ($courseOptions as $id => $name) {
    $selected = ($id == $selectedCourse) ? "selected" : "";
    echo "<option value='$id' $selected>" . htmlspecialchars($name) . "</option>";
}
echo "</select> ";

echo "Student: <input type='text' name='student' value='" . htmlspecialchars($studentFilter) . "'> ";
echo "<input type='submit' value='Filter'>";
echo "</form><br>";

if ($selectedCourse) {
    // Step 1: Get distinct questions for selected course
    $stmt = $conn->prepare("
        SELECT DISTINCT fb_item.id, fb_item.name 
        FROM elp_feedback f
        JOIN elp_feedback_item fb_item ON fb_item.feedback = f.id
        WHERE f.course = ?
        ORDER BY fb_item.id
    ");
    $stmt->bind_param("i", $selectedCourse);
    $stmt->execute();
    $qres = $stmt->get_result();

    $questionMap = [];
    while ($row = $qres->fetch_assoc()) {
        $questionMap[$row['id']] = $row['name'];
    }

    // Step 2: Get feedback responses
    $sql = "
        SELECT 
            u.id AS user_id,
            CONCAT(u.firstname, ' ', u.lastname) AS student,
            c.fullname AS course,
            fb_item.id AS question_id,
            fb_item.name AS question,
            fb_value.value AS response
        FROM elp_feedback f
        JOIN elp_feedback_item fb_item ON fb_item.feedback = f.id
        JOIN elp_feedback_completed fb_comp ON fb_comp.feedback = f.id
        JOIN elp_feedback_value fb_value ON fb_value.completed = fb_comp.id AND fb_value.item = fb_item.id
        JOIN elp_user u ON u.id = fb_comp.userid
        JOIN elp_course c ON c.id = f.course
        WHERE c.id = ?
    ";

    $types = 'i';
    $params = [$selectedCourse];

    if (!empty($studentFilter)) {
        $sql .= " AND CONCAT(u.firstname, ' ', u.lastname) LIKE ?";
        $types .= 's';
        $params[] = "%$studentFilter%";
    }

    $sql .= " ORDER BY student, question_id";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    // Step 3: Pivot the data
    $data = [];
    $totals = [];

    while ($row = $result->fetch_assoc()) {
        $uid = $row['user_id'];
        $student = $row['student'];
        $course = $row['course'];
        $qid = $row['question_id'];
        $response = $row['response'];

        $data[$uid]['student'] = $student;
        $data[$uid]['course'] = $course;
        $data[$uid]['responses'][$qid] = $response;

        $totals[$uid] = ($totals[$uid] ?? 0) + (is_numeric($response) ? $response : 0);
    }

    // Step 4: Output pivot table
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Student</th><th>Course</th>";

    foreach ($questionMap as $qid => $qname) {
        echo "<th>" . htmlspecialchars($qname) . "</th>";
    }

    echo "<th>Total</th></tr>";

    foreach ($data as $uid => $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['student']) . "</td>";
        echo "<td>" . htmlspecialchars($row['course']) . "</td>";
        foreach ($questionMap as $qid => $qname) {
            $val = $row['responses'][$qid] ?? '';
            echo "<td>" . htmlspecialchars($val) . "</td>";
        }
        echo "<td>" . $totals[$uid] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p><i>Please select a course to view feedback.</i></p>";
}

$conn->close();
?>