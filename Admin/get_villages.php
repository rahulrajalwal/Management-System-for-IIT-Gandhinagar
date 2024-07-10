<?php
// Database connection
include '../Includes/dbcon.php';

$response = array();

$sql = "SELECT village_name FROM villages";
$result = $conn->query($sql);

if ($result === false) {
    $response['success'] = false;
    $response['message'] = 'Database query error: ' . $conn->error;
} else {
    if ($result->num_rows > 0) {
        $villages = array();
        while ($row = $result->fetch_assoc()) {
            $villages[] = $row['village_name'];
        }
        $response['success'] = true;
        $response['villages'] = $villages;
    } else {
        $response['success'] = false;
        $response['message'] = 'No villages found.';
    }
}

echo json_encode($response);

$conn->close();
?>
