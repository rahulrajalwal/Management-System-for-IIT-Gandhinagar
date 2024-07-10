<?php
include '../Includes/dbcon.php';

if (isset($_GET['classId'])) {
    $classId = $_GET['classId'];

    $query = "SELECT Id, classArmName FROM tblclassarms WHERE classId = '$classId'";
    $result = mysqli_query($conn, $query);

    echo "<option value=''>Select Class Arm</option>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='" . $row['Id'] . "'>" . $row['classArmName'] . "</option>";
    }
}
?>

