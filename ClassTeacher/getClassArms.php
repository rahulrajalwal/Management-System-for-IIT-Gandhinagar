<?php
include '../Includes/dbcon.php';

if (isset($_POST['classId'])) {
    $classId = $_POST['classId'];
    $qry = "SELECT * FROM tblclassarms WHERE classId = '$classId' ORDER BY classArmName ASC";
    $result = $conn->query($qry);

    if ($result->num_rows > 0) {
        echo '<option value="">--Select Class Arm--</option>';
        while ($rows = $result->fetch_assoc()) {
            echo '<option value="'.$rows['Id'].'">'.$rows['classArmName'].'</option>';
        }
    } else {
        echo '<option value="">No Class Arms Available</option>';
    }
}
?>
