<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../Includes/dbcon.php';
include '../Includes/session.php';
$statusMsg = "";

// Check if the form has been submitted
if (isset($_POST['view'])) {
    $aadharNumber = $_POST['aadharNumber'];
    $type = $_POST['type'];
    $query = "";

    if ($type == "1") { // All Attendance
        $query = "
            SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, tblclass.className, tblclassarms.classArmName, tblstudents.surname, tblstudents.name, tblstudents.secondName, tblstudents.aadharNumber
            FROM tblattendance
            INNER JOIN tblstudent_class ON tblstudent_class.student_id = tblattendance.aadharNumber
            INNER JOIN tblclass ON tblclass.Id = tblstudent_class.class_id
            INNER JOIN tblclassarms ON tblclassarms.Id = tblstudent_class.class_arm_id
            INNER JOIN tblstudents ON tblstudents.aadharNumber = tblattendance.aadharNumber
            WHERE tblattendance.aadharNumber = '$aadharNumber'
            AND tblstudent_class.class_id = $_SESSION[classId]
            AND tblstudent_class.class_arm_id = $_SESSION[classArmId]
        ";
    } elseif ($type == "2") { // Single Date Attendance
        $singleDate = $_POST['singleDate'];
        $query = "
            SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, tblclass.className, tblclassarms.classArmName, tblstudents.surname, tblstudents.name, tblstudents.secondName, tblstudents.aadharNumber
            FROM tblattendance
            INNER JOIN tblstudent_class ON tblstudent_class.student_id = tblattendance.aadharNumber
            INNER JOIN tblclass ON tblclass.Id = tblstudent_class.class_id
            INNER JOIN tblclassarms ON tblclassarms.Id = tblstudent_class.class_arm_id
            INNER JOIN tblstudents ON tblstudents.aadharNumber = tblattendance.aadharNumber
            WHERE DATE(tblattendance.dateTimeTaken) = '$singleDate'
            AND tblattendance.aadharNumber = '$aadharNumber'
            AND tblstudent_class.class_id = $_SESSION[classId]
            AND tblstudent_class.class_arm_id = $_SESSION[classArmId]
        ";
    } elseif ($type == "3") { // Date Range Attendance
        $fromDate = $_POST['fromDate'];
        $toDate = $_POST['toDate'];
        $query = "
            SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, tblclass.className, tblclassarms.classArmName, tblstudents.surname, tblstudents.name, tblstudents.secondName, tblstudents.aadharNumber
            FROM tblattendance
            INNER JOIN tblstudent_class ON tblstudent_class.student_id = tblattendance.aadharNumber
            INNER JOIN tblclass ON tblclass.Id = tblstudent_class.class_id
            INNER JOIN tblclassarms ON tblclassarms.Id = tblstudent_class.class_arm_id
            INNER JOIN tblstudents ON tblstudents.aadharNumber = tblattendance.aadharNumber
            WHERE DATE(tblattendance.dateTimeTaken) BETWEEN '$fromDate' AND '$toDate'
            AND tblattendance.aadharNumber = '$aadharNumber'
            AND tblstudent_class.class_id = $_SESSION[classId]
            AND tblstudent_class.class_arm_id = $_SESSION[classArmId]
        ";
    }

    $rs = $conn->query($query);
    $num = $rs->num_rows;
    $sn = 0;

    if ($num > 0) {
        $attendanceRows = "";
        while ($rows = $rs->fetch_assoc()) {
            $status = $rows['status'] == 1 ? 'Present' : 'Absent';
            $color = $rows['status'] == 1 ? '#00FF00' : '#FF0000';
            $sn++;
            $attendanceRows .= "
                <tr>
                    <td>$sn</td>
                    <td>{$rows['surname']}</td>
                    <td>{$rows['name']}</td>
                    <td>{$rows['secondName']}</td>
                    <td>{$rows['aadharNumber']}</td>
                    <td>{$rows['className']}</td>
                    <td>{$rows['classArmName']}</td>
                    <td style='background-color: $color'>$status</td>
                    <td>{$rows['dateTimeTaken']}</td>
                </tr>
            ";
        }
    } else {
        $statusMsg = "<div class='alert alert-danger' role='alert'>No Record Found!</div>";
    }
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="img/logo/attnlg.jpg" rel="icon">
  <title>Dashboard</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">

  <script>
    function toggleDateFields() {
      var type = document.getElementById("type").value;
      if (type == "2") {
        document.getElementById("singleDate").style.display = "block";
        document.getElementById("dateRange").style.display = "none";
      } else if (type == "3") {
        document.getElementById("singleDate").style.display = "none";
        document.getElementById("dateRange").style.display = "block";
      } else {
        document.getElementById("singleDate").style.display = "none";
        document.getElementById("dateRange").style.display = "none";
      }
    }
  </script>
</head>

<body id="page-top">
  <div id="wrapper">
    <?php include "Includes/sidebar.php"; ?>
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <?php include "Includes/topbar.php"; ?>

        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">View Student Attendance</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">View Student Attendance</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">View Student Attendance</h6>
                  <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="form-group row mb-3">
                            <div class="col-xl-6">
                                <label class="form-control-label">Select Student<span class="text-danger ml-2">*</span></label>
                                <select required name="aadharNumber" class="form-control mb-3">
                                    <option value="">--Select Student--</option>
                                    <?php
                                    // Fetch students based on class_id and class_arm_id
                                    $qry = "SELECT s.aadharNumber, s.surname, s.name, s.secondName 
                                            FROM tblstudents s
                                            INNER JOIN tblstudent_class sc ON s.Id = sc.student_id
                                            WHERE sc.class_id = $_SESSION[classId] 
                                            AND sc.class_arm_id = $_SESSION[classArmId] 
                                            ORDER BY s.surname ASC";
                                    
                                    $result = $conn->query($qry);
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . $row['aadharNumber'] . '">' . $row['surname'] . ' ' . $row['name'] . ' ' . $row['secondName'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Type<span class="text-danger ml-2">*</span></label>
                        <select required name="type" id="type" onchange="toggleDateFields()" class="form-control mb-3">
                          <option value="">--Select--</option>
                          <option value="1">All</option>
                          <option value="2">By Single Date</option>
                          <option value="3">By Date Range</option>
                        </select>
                      </div>
                    </div>

                    <div id="singleDate" style="display:none;" class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Date<span class="text-danger ml-2">*</span></label>
                        <input type="date" name="singleDate" class="form-control mb-3">
                      </div>
                    </div>

                    <div id="dateRange" style="display:none;" class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">From Date<span class="text-danger ml-2">*</span></label>
                        <input type="date" name="fromDate" class="form-control mb-3">
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">To Date<span class="text-danger ml-2">*</span></label>
                        <input type="date" name="toDate" class="form-control mb-3">
                      </div>
                    </div>

                    <button type="submit" name="view" class="btn btn-primary">View Attendance</button>
                  </form>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-12">
                  <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                      <h6 class="m-0 font-weight-bold text-primary">Class Attendance</h6>
                    </div>
                    <div class="table-responsive p-3">
                      <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                        <thead class="thead-light">
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
                        <tbody>
                          <?php
                          if (isset($attendanceRows)) {
                            echo $attendanceRows;
                          }
                          ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
      <?php include "Includes/footer.php"; ?>
    </div>
  </div>

  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#dataTable').DataTable();
      $('#dataTableHover').DataTable();
    });
  </script>
</body>
</html>
