<?php
// Include your database connection file
include 'db_con.php';

// Start session (assuming you're using sessions for authentication)
session_start();

// Get teacher ID from session
$teacher_id = $_SESSION['teacher_id']; // Update this according to your authentication logic

// Get current timestamp
$login_timestamp = date('Y-m-d H:i:s');

// Insert login activity into database
$sql = "INSERT INTO teacher_login_activity (teacher_id, login_timestamp) VALUES ('$teacher_id', '$login_timestamp')";
if (mysqli_query($conn, $sql)) {
    echo "Login activity logged successfully.";
} else {
    echo "Error logging login activity: " . mysqli_error($conn);
}
mysqli_close($conn);
?>

<?php
// Include your database connection file
include 'db_con.php';

// Fetch login activity from the database
$sql = "SELECT * FROM teacher_login_activity";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "Teacher ID: " . $row['teacher_id'] . " - Login Time: " . $row['login_timestamp'] . "<br>";
    }
} else {
    echo "No login activity found.";
}
mysqli_close($conn);
?>
