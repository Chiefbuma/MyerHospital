<?php

date_default_timezone_set("Etc/GMT+8");

session_start();

require_once 'config.php';
require_once 'class.php';  // Assuming this contains the db_class definition

try {
    $db = new db_class();

    // Get the logged-in user's ID
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("User not logged in.");
    }
    $user_id = $_SESSION['user_id'];

    // Fetch the role and cohort
    $query = "SELECT branch_id, email, role, cohort_id FROM users WHERE id = ?";
    $stmt = $db->conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        $user_location = $row['branch_id'];
        $user_role = $row['role'];
        $user_cohort = $row['cohort_id'];
        $user_email = $row['email'];
    } else {
        throw new Exception("Error fetching user details.");
    }
    $stmt->close();

    // Query to fetch branch name based on branch_id
    $branch_query = "SELECT branch_name FROM branch WHERE branch_id = ?";
    $stmt = $db->conn->prepare($branch_query);
    $stmt->bind_param("i", $user_location);
    $stmt->execute();
    $branch_result = $stmt->get_result();

    if ($branch_result && $branch_row = $branch_result->fetch_assoc()) {
        $branch_name = $branch_row['branch_name'];
    } else {
        $branch_name = "1=1"; // select all branches
    }
    $stmt->close();

    // Retrieve filters
    $from_date = isset($_GET['from_date']) ? $_GET['from_date'] : null;
    $to_date = isset($_GET['to_date']) ? $_GET['to_date'] : null;

    // Convert dates to Y-M-D format
    if ($from_date) {
        $from_date = DateTime::createFromFormat('Y-m-d', $from_date)->format('Y-m-d');
    }
    if ($to_date) {
        $to_date = DateTime::createFromFormat('Y-m-d', $to_date)->format('Y-m-d');
    }

    // Set location filter dynamically
    if ($user_role == 'admin') {
        $condition = "1=1"; // No filtering, fetch all locations
    } else {
        $selected_location = $user_location;
        $condition = "nutrition.branch_id = ?";
    }

    // Add date filters
    if ($from_date && $to_date) {
        $condition .= " AND nutrition.visit_date BETWEEN ? AND ?";
    } elseif ($from_date) {
        $condition .= " AND nutrition.visit_date >= ?";
    } elseif ($to_date) {
        $condition .= " AND nutrition.visit_date <= ?";
    }

    // Query to fetch data based on the date range
    $query = "SELECT 
        nutrition.*,
        patient.*,
        branch.*,
        cohort.*,
        calls.*,
        branch.branch_name AS branch,
        cohort.cohort_name AS cohort,
        scheme.*
    FROM 
        nutrition
    INNER JOIN 
        patient ON nutrition.patient_id = patient.patient_id
    LEFT JOIN 
        calls ON patient.patient_id = calls.patient_id
    LEFT JOIN 
        branch ON patient.branch_id = branch.branch_id
    LEFT JOIN 
        cohort ON patient.cohort_id = cohort.cohort_id
    LEFT JOIN 
        scheme ON nutrition.scheme_id = scheme.scheme_id
    WHERE
        $condition";

    $stmt = $db->conn->prepare($query);

    // Bind parameters dynamically
    $params = [];
    $types = '';
    if ($user_role != 'admin') {
        $types .= 'i';
        $params[] = $selected_location;
    }
    if ($from_date) {
        $types .= 's';
        $params[] = $from_date;
    }
    if ($to_date) {
        $types .= 's';
        $params[] = $to_date;
    }
    $stmt->bind_param($types, ...$params);

    // Execute the query
    $stmt->execute();
    $report = $stmt->get_result();

    if ($report->num_rows > 0) {
        while ($row2 = $report->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . date("d-m-Y", strtotime($row2['visit_date'])) . "</td>";
            echo "<td>" . htmlspecialchars($row2['firstname']) . " " . htmlspecialchars($row2['lastname']) . "</td>";
            echo "<td>" . htmlspecialchars($row2['patient_no']) . "</td>";
            echo "<td>" . htmlspecialchars($row2['patient_status']) . "</td>";
            echo "<td>" . htmlspecialchars($row2['branch']) . "</td>";
            echo "<td>" . htmlspecialchars($row2['cohort']) . "</td>";
            echo "<td>" . htmlspecialchars($row2['revenue']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='22' class='text-center'>No Data Found</td></tr>";
    }
    $stmt->close();
} catch (Exception $e) {
    echo "<tr><td colspan='22' class='text-center'>Exception: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
} finally {
    // Close the database connection
    if (isset($db->conn)) {
        $db->conn->close();
    }
}
