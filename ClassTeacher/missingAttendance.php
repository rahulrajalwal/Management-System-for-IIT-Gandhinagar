<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
$className = isset($rrw['className']) ? $rrw['className'] : 'N/A';
$classArmName = isset($rrw['classArmName']) ? $rrw['classArmName'] : 'N/A';

// Fetch missing attendance dates
$queryDates = "SELECT DISTINCT dateTimeTaken 
               FROM tblattendance 
               WHERE classId = '$_SESSION[classId]' AND classarmId = '$_SESSION[classArmId]' AND status = '0'";
$rsDates = $conn->query($queryDates);

if (!$rsDates) {
    die("Query failed: " . $conn->error);
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
    <title>Missing Attendance</title>
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
                        <h1 class="h3 mb-0 text-gray-800">Missing Attendance for Class: <?php echo htmlspecialchars($className . ' - ' . htmlspecialchars($classArmName)); ?></h1>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Missing Attendance</li>
                        </ol>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Table -->
                            <div class="card mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Dates with Missing Attendance</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table align-items-center table-flush table-hover">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $numDates = $rsDates->num_rows;
                                                $sn = 0;
                                                if ($numDates > 0) {
                                                    while ($rowsDates = $rsDates->fetch_assoc()) {
                                                        $sn++;
                                                        $date = htmlspecialchars($rowsDates['dateTimeTaken']);
                                                        echo "
                                                        <tr>
                                                            <td>$sn</td>
                                                            <td>$date</td>
                                                            <td><a href='takeAttendance.php?date=$date' class='btn btn-primary'>Take Attendance</a></td>
                                                        </tr>";
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='3' class='text-center'>No Missing Attendance Dates Found!</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- Table -->
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

    <!-- Page level custom script -->
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable(); // ID From dataTable 
            $('#dataTableHover').DataTable(); // ID From dataTable with Hover
        });
    </script>
</body>

</html>
