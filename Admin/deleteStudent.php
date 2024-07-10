<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$statusMsg = "";

if (isset($_GET['id'])) {
    $studentId = $_GET['id'];

    // Prepare the statement
    $query = "DELETE FROM tblstudents WHERE Id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        // Bind parameters and execute statement
        $stmt->bind_param("i", $studentId);
        $stmt->execute();

        // Check if the delete was successful
        if ($stmt->affected_rows > 0) {
            $statusMsg = "<div class='alert alert-success'>Student deleted successfully!</div>";
        } else {
            $statusMsg = "<div class='alert alert-danger'>An error occurred: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        $statusMsg = "<div class='alert alert-danger'>An error occurred: " . $conn->error . "</div>";
    }
    
    // Store status message in session to display after redirect
    $_SESSION['statusMsg'] = $statusMsg;
    header("Location: viewstudents.php");
    exit();
}

// In viewStudents.php, you would check and display the status message like this:
if (isset($_SESSION['statusMsg'])) {
    echo $_SESSION['statusMsg'];
    unset($_SESSION['statusMsg']);
}
?>
