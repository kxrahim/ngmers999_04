<?php

    require_once('includes/connection.php');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    if (isset($_POST['query'])) {
        $query = $conn->real_escape_string($_POST['query']);
    
        // Search for matching student names
        $sql = "SELECT id, 
                       name AS cohort_name
                FROM elp_cohort 
                WHERE name LIKE '%$query%' LIMIT 5";
        $result = $conn->query($sql);
    
        if ($result->num_rows > 0) {
            // Display suggestions
            while ($row = $result->fetch_assoc()) {
                echo '<div class="suggestion-item" data-id="' . $row['id'] . '">' . strtoupper($row['cohort_name']) . '</div>';
            }
        } else {
            echo '<div>No results found</div>';
        }
    }
    
    $conn->close();
?>
