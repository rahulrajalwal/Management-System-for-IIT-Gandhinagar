<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Start the session
session_start();

if (isset($_SESSION['statusMsg'])) {
    echo $_SESSION['statusMsg'];
    unset($_SESSION['statusMsg']);
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
  <?php include 'includes/title.php'; ?>
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
            <h1 class="h3 mb-0 text-gray-800">View Students</h1>
            <!-- Search Form -->
            <form class="form-inline my-2 my-lg-0" method="GET" action="">
              <input class="form-control mr-sm-2" type="search" placeholder="Search by name or Aadhaar number" aria-label="Search" name="search">
              <button class="btn btn-outline-success my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
            </form>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Student List</h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>ID Number</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        // Fetch data from the database
                        $search = isset($_GET['search']) ? $_GET['search'] : '';
                        $query = "SELECT Id, name, aadharNumber FROM tblstudents";
                        if ($search) {
                          $query .= " WHERE name LIKE '%$search%' OR aadharNumber LIKE '%$search%'";
                        }
                        $result = mysqli_query($conn, $query);

                        // Check if there are any students
                        if (mysqli_num_rows($result) > 0) {
                          // Loop through each row and display student data
                          while ($row = mysqli_fetch_array($result)) {
                            echo "<tr>";
                            echo "<td><a href='student_details.php?id=" . $row['Id'] . "'>" . $row['name'] . "</a></td>";
                            echo "<td>" . $row['aadharNumber'] . "</td>";
                            echo "<td>";
                            echo "<a href='enrollment.php?id=" . $row['Id'] . "' class='btn btn-info btn-sm'>Enroll</a> ";
                            echo "<a href='editStudent.php?id=" . $row['Id'] . "' class='btn btn-primary btn-sm'>Edit</a> ";
                            echo "<a href='deleteStudent.php?id=" . $row['Id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this student?\");'>Delete</a> ";
                            echo "<a href='feedback.php?id=" . $row['Id'] . "' class='btn btn-success btn-sm'>Feedback</a> ";
                            echo "</td>";
                            echo "</tr>";
                          }
                        } else {
                          echo "<tr><td colspan='3'>No students found.</td></tr>";
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
        <!---Container Fluid-->
      </div>
      <!-- Footer -->
      <?php include "../Includes/footer.php"; ?>
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
