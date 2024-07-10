<?php
error_reporting(0);
ini_set('display_errors', 1);

include '../Includes/dbcon.php';
include '../Includes/session.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- HTML code here -->
</head>
<body>
  <!-- Body code here -->
  <table border="1">
    <thead>
      <tr>
        <th>#</th>
        <th>Surname</th>
        <th>Name</th>
        <th>Second Name</th>
        <th>Aadhar Number</th>
        <th>Class</th>
        <th>Class Arm</th>
        <th>Status</th>
        <th>Date</th>
      </tr>
    </thead>

    <?php
    $filename = "Attendance_list_" . date("Y-m-d") . ".xls";
    $dateTaken = date("Y-m-d");

    $cnt = 1;
    $ret = mysqli_query($conn, "SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, tblclass.className, tblclassarms.classArmName, tblstudents.surname, tblstudents.name, tblstudents.secondName, tblstudents.aadharNumber
    FROM tblattendance
    INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
    INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
    INNER JOIN tblstudents ON tblstudents.aadharNumber = tblattendance.aadharNumber
    WHERE tblattendance.dateTimeTaken = '$dateTaken' AND tblattendance.classId = $_SESSION[classId] AND tblattendance.classArmId = $_SESSION[classArmId]");

    if (mysqli_num_rows($ret) > 0) {
        while ($row = mysqli_fetch_array($ret)) {
            $statusText = $row['status'] == 1 ? 'Present' : 'Absent';
            $statusColor = $row['status'] == 1 ? 'green' : 'red';

            echo "
                <tr>
                    <td>" . $cnt . "</td>
                    <td>" . $row['surname'] . "</td>
                    <td>" . $row['name'] . "</td>
                    <td>" . $row['secondName'] . "</td>
                    <td>" . $row['aadharNumber'] . "</td>
                    <td>" . $row['className'] . "</td>
                    <td>" . $row['classArmName'] . "</td>
                    <td style='background-color: $stastusColor'>" . $statusText . "</td>
                    <td>" . $row['dateTimeTaken'] . "</td>
                </tr>";
            $cnt++;
        }
    }
    ?>
  </table>

  <script>
    // JavaScript code here
  </script>

<?php
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=$filename");
    header("Pragma: no-cache");
    header("Expires: 0");
?>
</body>
</html>
