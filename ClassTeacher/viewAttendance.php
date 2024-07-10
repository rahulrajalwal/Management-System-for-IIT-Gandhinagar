<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Fetch attendance records
$searchQuery = "";
$searchDate = "";
if (isset($_POST['search'])) {
    $searchQuery = $_POST['searchQuery'];
    $searchDate = $_POST['searchDate'];
    $statusFilter = '';
    if (strtolower($searchQuery) == 'present') {
        $statusFilter = '1';
    } elseif (strtolower($searchQuery) == 'absent') {
        $statusFilter = '0';
    }

    $query = "SELECT tblstudents.surname, tblstudents.name, tblstudents.secondName, tblattendance.status, tblattendance.dateTimeTaken
              FROM tblstudents
              INNER JOIN tblattendance ON tblstudents.aadharNumber = tblattendance.aadharNumber
              WHERE tblattendance.classId = '$_SESSION[classId]' AND tblattendance.classArmId = '$_SESSION[classArmId]'
              AND (tblstudents.surname LIKE '%$searchQuery%' OR tblstudents.name LIKE '%$searchQuery%' OR tblstudents.secondName LIKE '%$searchQuery%' OR tblattendance.status = '$statusFilter')";

    if (!empty($searchDate)) {
        $query .= " AND DATE(tblattendance.dateTimeTaken) = '$searchDate'";
    }

    $query .= " ORDER BY tblattendance.dateTimeTaken DESC";
} else {
    $query = "SELECT tblstudents.surname, tblstudents.name, tblstudents.secondName, tblattendance.status, tblattendance.dateTimeTaken
              FROM tblstudents
              INNER JOIN tblattendance ON tblstudents.aadharNumber = tblattendance.aadharNumber
              WHERE tblattendance.classId = '$_SESSION[classId]' AND tblattendance.classArmId = '$_SESSION[classArmId]'
              ORDER BY tblattendance.dateTimeTaken DESC";
}

$result = mysqli_query($conn, $query);
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
  <title>View Student Attendance</title>
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
            <h1 class="h3 mb-0 text-gray-800">View Student Attendance</h1>
            <form class="form-inline" method="post" action="">
              <input class="form-control mr-sm-2" type="search" name="searchQuery" placeholder="Search by name or status" aria-label="Search" value="<?php echo $searchQuery; ?>">
              <input class="form-control mr-sm-2" type="date" name="searchDate" aria-label="Search by date" value="<?php echo $searchDate; ?>">
              <button class="btn btn-outline-success my-2 my-sm-0" type="submit" name="search">Search</button>
            </form>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">View Student Attendance</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Attendance Table -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Student Attendance Records</h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table align-items-center table-flush table-hover">
                      <thead class="thead-light">
                        <tr>
                          <th>#</th>
                          <th>Surname</th>
                          <th>Name</th>
                          <th>Second Name</th>
                          <th>Status</th>
                          <th>Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            $count = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $statusText = $row['status'] == 1 ? 'Present' : 'Absent';
                                $statusColor = $row['status'] == 1 ? 'green' : 'red';
                                echo "<tr>";
                                echo "<td>" . $count . "</td>";
                                echo "<td>" . $row['surname'] . "</td>";
                                echo "<td>" . $row['name'] . "</td>";
                                echo "<td>" . $row['secondName'] . "</td>";
                                echo "<td style='color:" . $statusColor . "'>" . $statusText . "</td>";
                                echo "<td>" . $row['dateTimeTaken'] . "</td>";
                                echo "</tr>";
                                $count++;
                            }
                        } else {
                            echo "<tr><td colspan='6'>No attendance records found.</td></tr>";
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
</body>

</html>
