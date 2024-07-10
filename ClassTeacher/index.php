<?php 
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Get class and arm details assigned to the logged-in teacher
$query = "SELECT tblclass.Id as classId, tblclass.className, tblclassarms.Id as classArmId, tblclassarms.classArmName 
          FROM tblteacherclass
          INNER JOIN tblclass ON tblclass.Id = tblteacherclass.classId
          INNER JOIN tblclassarms ON tblclassarms.Id = tblteacherclass.classArmId
          WHERE tblteacherclass.teacherId = '$_SESSION[userId]'";

$classArmResult = $conn->query($query);
$classArms = $classArmResult->num_rows;

$today = date('Y-m-d');
$lastWeek = date('Y-m-d', strtotime('-7 days'));

// Check if form is submitted to set classId and classArmId in session
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['classId'] = $_POST['classId'];
    $_SESSION['classArmId'] = $_POST['classArmId'];
}

$classId = $_SESSION['classId'];
$classArmId = $_SESSION['classArmId'];

// Fetch attendance details
$pendingAttendanceQuery = "SELECT classId, classArmId, dateTimeTaken 
                           FROM tblattendance 
                           WHERE classId = '$classId' AND classArmId = '$classArmId' 
                           AND dateTimeTaken BETWEEN '$lastWeek' AND '$today'
                           ORDER BY dateTimeTaken DESC";
$pendingAttendanceResult = $conn->query($pendingAttendanceQuery);

$todayAttendanceQuery = "SELECT * FROM tblattendance 
                         WHERE classId = '$classId' AND classArmId = '$classArmId' 
                         AND dateTimeTaken = '$today'";
$todayAttendanceResult = $conn->query($todayAttendanceQuery);
$todayAttendanceRecorded = $todayAttendanceResult->num_rows > 0;

// Fetch the number of students added today by the teacher
$teacherId = $_SESSION['userId'];
$studentsAddedTodayQuery = "SELECT COUNT(*) as count 
                            FROM tblstudents 
                            WHERE DATE(dateCreated) = '$today' AND AddedBy = (SELECT emailAddress FROM tblclassteacher WHERE Id = '$teacherId')";
$studentsAddedTodayResult = $conn->query($studentsAddedTodayQuery);
$studentsAddedTodayCount = $studentsAddedTodayResult->fetch_assoc()['count'];
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
   <?php include "Includes/sidebar.php";?>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
           <?php include "Includes/topbar.php";?>
        <!-- Topbar -->
        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Class Teacher Dashboard</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </div>

          <div class="row mb-3">
            <!-- Add Dropdown for Class and Class Arm -->
            <div class="col-xl-12 col-md-12 mb-4">
              <form method="POST" action="">
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="class">Select Class:</label>
                    <select class="form-control" id="class" name="classId">
                      <?php 
                      $classArmResult->data_seek(0); // Reset pointer
                      while ($row = $classArmResult->fetch_assoc()) : ?>
                        <option value="<?php echo $row['classId']; ?>" <?php if ($row['classId'] == $_SESSION['classId']) : ?>selected="selected"<?php endif; ?>><?php echo $row['className']; ?></option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="classArm">Select Class Arm:</label>
                    <select class="form-control" id="classArm" name="classArmId">
                      <?php 
                      $classArmResult->data_seek(0); // Reset pointer
                      while ($row = $classArmResult->fetch_assoc()) : ?>
                        <option value="<?php echo $row['classArmId']; ?>" <?php if ($row['classArmId'] == $_SESSION['classArmId']) : ?>selected="selected"<?php endif; ?>><?php echo $row['classArmName']; ?></option>
                      <?php endwhile; ?>
                    </select>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
              </form>
            </div>
          </div>

          <div class="row mb-3">
            <!-- Students Card -->
            <?php 
            $query1 = mysqli_query($conn, "SELECT * FROM tblstudent_class WHERE class_id = '$classId' AND class_arm_id = '$classArmId'");
            $students = mysqli_num_rows($query1);
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Students</div>
                      <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $students;?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-users fa-2x text-info"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Classes Card -->
            <?php 
            $query1 = mysqli_query($conn, "SELECT DISTINCT classId FROM tblteacherclass WHERE teacherId = '$_SESSION[userId]'");                       
            $class = mysqli_num_rows($query1);
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Classes</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $class;?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-chalkboard fa-2x text-primary"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Class Arms Card -->
            <?php 
            $query1 = mysqli_query($conn, "SELECT * FROM tblclassarms WHERE Id IN (SELECT classArmId FROM tblteacherclass WHERE teacherId = '$_SESSION[userId]')");                       
            $classArms = mysqli_num_rows($query1);
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Class Arms</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $classArms;?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-code-branch fa-2x text-success"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Total Student Attendance Today Card -->
            <?php
            // Fetch today's date
            $todayDate = date('Y-m-d');
            
            // Modify the query to include today's date and present status
            $query1 = mysqli_query($conn, "SELECT * FROM tblattendance WHERE classId = '$classId' AND classArmId = '$classArmId' AND dateTimeTaken = '$todayDate' AND status = 1");
            $totAttendance = mysqli_num_rows($query1);
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Total Student Attendance Today</div>
                      <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $totAttendance;?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-users fa-2x text-warning"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Today's Attendance Card -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Today's Attendance</div>
                      <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                        <?php echo $todayAttendanceRecorded ? 'Recorded' : 'Not Recorded'; ?>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-calendar-check fa-2x text-primary"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Students Added Today Card -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Students Added Today</div>
                      <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $studentsAddedTodayCount; ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-user-plus fa-2x text-success"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--Row-->

        </div>
        <!--Container Fluid-->
      </div>
      <!-- Footer -->
      <?php include 'includes/footer.php'; ?>
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
  <script src="../vendor/chart.js/Chart.min.js"></script>
  <script src="js/demo/chart-area-demo.js"></script>
</body>

</html>
