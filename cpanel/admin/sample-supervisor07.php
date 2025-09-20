<?php
// DB connection
require_once('../../config.php'); // Moodle DB connection config


$conn = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filters
$courseId = isset($_GET['course']) ? intval($_GET['course']) : 0;
$studentFilter = isset($_GET['student']) ? $_GET['student'] : '';

// Get course list for dropdown
$courses = [];
$coursesRes = $conn->query("SELECT id, fullname FROM elp_course ORDER BY fullname");
while ($row = $coursesRes->fetch_assoc()) {
    $courses[$row['id']] = $row['fullname'];
}

// Display filter form
echo "<form method='GET'>";
echo "Course: <select name='course'>";
echo "<option value=''>-- Select Course --</option>";
foreach ($courses as $id => $name) {
    $selected = ($id == $courseId) ? "selected" : "";
    echo "<option value='$id' $selected>" . htmlspecialchars($name) . "</option>";
}
echo "</select> ";

echo "Student: <input type='text' name='student' value='" . htmlspecialchars($studentFilter) . "'> ";
echo "<input type='submit' value='Filter'>";
echo "</form><br>";

// Only run query if course selected
if ($courseId > 0) {
    // Step 1: Get all questions for selected course
    $questions = [];
    $qres = $conn->prepare("
        SELECT fb_item.id, fb_item.name 
        FROM elp_feedback_item fb_item
        JOIN elp_feedback fb ON fb.id = fb_item.feedback
        WHERE fb.course = ?
    ");
    $qres->bind_param("i", $courseId);
    $qres->execute();
    $qresult = $qres->get_result();
    while ($row = $qresult->fetch_assoc()) {
        $questions[$row['id']] = $row['name'];
    }

    // Step 2: Build main SQL
    $sql = "
        SELECT 
            u.id AS user_id,
            CONCAT(u.firstname, ' ', u.lastname) AS student_name,
            c.fullname AS course_name,
            fb_item.id AS question_id,
            fb_item.name AS question_text,
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

    $types = 'i';
    $params = [$courseId];

    if (!empty($studentFilter)) {
        $sql .= " AND CONCAT(u.firstname, ' ', u.lastname) LIKE ?";
        $types .= 's';
        $params[] = "%$studentFilter%";
    }

    $sql .= " ORDER BY u.lastname, fb_item.id";

    // Step 3: Execute and fetch
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    // Step 4: Organize data per student
    $studentData = []; // user_id => ['student_name' => ..., 'course' => ..., 'responses' => [qid => value]]
    while ($row = $result->fetch_assoc()) {
        $uid = $row['user_id'];
        if (!isset($studentData[$uid])) {
            $studentData[$uid] = [
                'student_name' => $row['student_name'],
                'course' => $row['course_name'],
                'responses' => []
            ];
        }
        $studentData[$uid]['responses'][$row['question_id']] = $row['response'];
    }

    // Step 5: Render pivot table
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Student</th><th>Course</th>";

    foreach ($questions as $qid => $qtext) {
        echo "<th>" . htmlspecialchars($qtext) . "</th>";
    }
    echo "<th>Total</th></tr>";

    foreach ($studentData as $student) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($student['student_name']) . "</td>";
        echo "<td>" . htmlspecialchars($student['course']) . "</td>";
        $total = 0;
        foreach ($questions as $qid => $qtext) {
            $val = isset($student['responses'][$qid]) ? $student['responses'][$qid] : '-';
            echo "<td>" . htmlspecialchars($val) . "</td>";
            if (is_numeric($val)) {
                $total += $val;
            }
        }
        echo "<td><strong>$total</strong></td>";
        echo "</tr>";
    }

    echo "</table>";
}
$conn->close();
?>