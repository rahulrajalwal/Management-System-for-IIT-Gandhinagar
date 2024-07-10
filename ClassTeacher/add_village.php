<?php
// Database connection
include '../Includes/dbcon.php';

$response = array();

if (isset($_POST['village'])) {
  $village = $conn->real_escape_string($_POST['village']);
  
  // Check if the village already exists
  $checkSql = "SELECT COUNT(*) AS count FROM villages WHERE village_name = '$village'";
  $checkResult = $conn->query($checkSql);
  if ($checkResult === false) {
    $response['success'] = false;
    $response['message'] = 'Database query error: ' . $conn->error;
  } else {
    $row = $checkResult->fetch_assoc();
    if ($row['count'] > 0) {
      $response['success'] = false;
      $response['message'] = 'Village already exists.';
    } else {
      $sql = "INSERT INTO villages (village_name) VALUES ('$village')";
      if ($conn->query($sql) === TRUE) {
        $response['success'] = true;
      } else {
        $response['success'] = false;
        $response['message'] = 'Database error: ' . $conn->error;
      }
    }
  }
} else {
  $response['success'] = false;
  $response['message'] = 'No village name provided.';
}

echo json_encode($response);

$conn->close();
?>
