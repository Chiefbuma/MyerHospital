<?php
header('Content-Type: application/json'); // Ensure response is JSON

require_once 'class.php';
require_once 'config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$patient_id = isset($_POST['patient_id']) ? intval($_POST['patient_id']) : 3;

if ($patient_id <= 0) {
    echo json_encode(["error" => "Invalid Patient ID"]);
    exit;
}

try {
    $query = $conn->prepare("SELECT * FROM medication_use WHERE patient_id = ?");
    $query->bind_param("i", $patient_id);
    $query->execute();
    $result = $query->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data); // Return the data as a JSON array
} catch (Exception $e) {
    echo json_encode(["error" => "Database Error: " . $e->getMessage()]);
}

$conn->close();
