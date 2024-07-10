<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Get class and class arm details
$query = "SELECT tblclass.className, tblclassarms.classArmName 
          FROM tblteacherclass
          INNER JOIN tblclass ON tblclass.Id = tblteacherclass.classId
          INNER JOIN tblclassarms ON tblclassarms.Id = tblteacherclass.classArmId
          WHERE tblteacherclass.teacherId = '$_SESSION[userId]'";
$rs = $conn->query($query);

if (!$rs) {
    die("Query failed: " . $conn->error);
}

$rrw = $rs->fetch_assoc();

// Get the date to take attendance for
$dateTaken = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

// Check if attendance has already been taken for the specified date
$qurty = mysqli_query($conn, "SELECT * FROM tblattendance WHERE classId = '$_SESSION[classId]' AND classarmId = '$_SESSION[classArmId]' AND dateTimeTaken = '$dateTaken'");
$count = mysqli_num_rows($qurty);

if ($count == 0) {
    $qus = mysqli_query($conn, "SELECT tblstudents.aadharNumber 
                                FROM tblstudent_class 
                                INNER JOIN tblstudents ON tblstudents.Id = tblstudent_class.student_id 
                                WHERE tblstudent_class.class_id = '$_SESSION[classId]' AND tblstudent_class.class_arm_id = '$_SESSION[classArmId]'");
    while ($ros = $qus->fetch_assoc()) {
        $qquery = mysqli_query($conn, "INSERT INTO tblattendance (aadharNumber, classId, classarmId, status, dateTimeTaken) 
                                       VALUES ('$ros[aadharNumber]', '$_SESSION[classId]', '$_SESSION[classArmId]', '0', '$dateTaken')");
    }
}

$statusMsg = "";

if (isset($_POST['save'])) {
    $aadharNumber = $_POST['aadharNumber'];
    $N = count($aadharNumber);
    $check = $_POST['check'];

    // Check if attendance has already been taken for the specified date
    $qurty = mysqli_query($conn, "SELECT * FROM tblattendance WHERE classId = '$_SESSION[classId]' AND classarmId = '$_SESSION[classArmId]' AND dateTimeTaken = '$dateTaken' AND status = '1'");
    $count = mysqli_num_rows($qurty);

    if ($count > 0) {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Attendance has already been taken for the specified date!</div>";
    } else {
        for ($i = 0; $i < $N; $i++) {
            if (isset($check[$i])) {
                $qquery = mysqli_query($conn, "UPDATE tblattendance SET status = '1' WHERE aadharNumber = '$check[$i]' AND dateTimeTaken = '$dateTaken'");

                if ($qquery) {
                    $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Attendance Taken Successfully!</div>";
                } else {
                    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error occurred!</div>";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="img/logo/attnlg.jpg" rel="icon">
  <title>Dashboard</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
    <?php include "Includes/sidebar.php"; ?>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
        <?php include "Includes/topbar.php"; ?>
        <!-- Topbar -->

        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Take Attendance (Date: <?php echo date("m-d-Y", strtotime($dateTaken)); ?>)</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">All Students in Class</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">All Students in (<?php echo htmlspecialchars($rrw['className'] . ' - ' . $rrw['classArmName']); ?>) Class</h6>
                  <h6 class="m-0 font-weight-bold text-danger">Note: <i>Click the checkboxes next to each student to take attendance!</i></h6>
                </div>
                <div class="card-body">
                  <?php echo $statusMsg; ?>
                  <form method="post">
                    <div class="table-responsive">
                      <table class="table align-items-center table-flush table-hover">
                        <thead class="thead-light">
                          <tr>
                            <th>#</th>
                            <th>Surname</th>
                            <th>Name</th>
                            <th>Second Name</th>
                            <th>Check</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $query = "SELECT tblstudents.Id, tblstudents.surname, tblstudents.name, tblstudents.secondName, tblstudents.aadharNumber, tblstudents.dateCreated
                                      FROM tblstudent_class
                                      INNER JOIN tblstudents ON tblstudents.Id = tblstudent_class.student_id
                                      WHERE tblstudent_class.class_id = '$_SESSION[classId]' AND tblstudent_class.class_arm_id = '$_SESSION[classArmId]'";
                            $rs = $conn->query($query);
                            if (!$rs) {
                                die("Query failed: " . $conn->error);
                            }
                            $num = $rs->num_rows;
                            $sn = 0;
                            if ($num > 0) {
                                while ($rows = $rs->fetch_assoc()) {
                                    $sn++;
                                    echo "
                                        <tr>
                                            <td>" . htmlspecialchars($sn) . "</td>
                                            <td>" . htmlspecialchars($rows['surname']) . "</td>
                                            <td>" . htmlspecialchars($rows['name']) . "</td>
                                            <td>" . htmlspecialchars($rows['secondName']) . "</td>
                                            <td><input name='check[]' type='checkbox' value='" . htmlspecialchars($rows['aadharNumber']) . "' class='form-control'></td>
                                        </tr>";
                                        echo "<input name='aadharNumber[]' value='" . htmlspecialchars($rows['aadharNumber']) . "' type='hidden' class='form-control'>";
                                }
                            } else {
                                echo "<div class='alert alert-danger' role='alert'>
                                    No Students Found!
                                </div>";
                            }
                          ?>
                        </tbody>
                      </table>
                    </div>
                    <br>
                    <button type="submit" name="save" class="btn btn-primary">Take Attendance</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Footer -->
      <?php include "Includes/footer.php"; ?>
      <!-- Footer -->
    </div>
  </div>

  <!-- Scroll to top -->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
  <!-- Page level plugins -->
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script>
    $(document).ready(function () {
      $('#dataTable').DataTable(); // ID From dataTable 
      $('#dataTableHover').DataTable(); // ID From dataTable with Hover
    });
  </script>
</body>

</html>
