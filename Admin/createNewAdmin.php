<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); // Start the session
include '../Includes/dbcon.php'; // Include your database configuration file

if (isset($_POST['submit'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Hash the password using md5

    // Insert into the database
    $sql = "INSERT INTO tbladmin (firstName, lastName, emailAddress, password) VALUES ('$firstName', '$lastName', '$email', '$password')";
    if (mysqli_query($conn, $sql)) {
        $statusMsg = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Admin created successfully.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
    } else {
        $statusMsg = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error creating admin: ' . mysqli_error($conn) . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
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
  <title>Create New Admin</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <style>
  .card-header {
    background-color: #4e73df;
    color: #fff;
    font-weight: bold;
  }

  /* Style for the submit button */
  .btn-primary {
    background-color: #4e73df;
    border-color: #4e73df;
  }

  .btn-primary:hover {
    background-color: #2e59d9;
    border-color: #2653d4;
  }
</style>

</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
    <?php include "Includes/sidebar.php";?>
    <!-- End of Sidebar -->

    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
        <?php include "Includes/topbar.php";?>
        <!-- End of Topbar -->

        <!-- Main Content -->
        <div class="container-fluid">
          <div class="row justify-content-center">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header text-center">
                  <h4>Create New Admin</h4>
                </div>
                <div class="card-body">
                  <?php if (isset($statusMsg)) echo $statusMsg; ?>
                  <form method="POST" action="">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="firstName">First Name</label>
                          <input type="text" class="form-control" id="firstName" name="firstName" required>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="lastName">Last Name</label>
                          <input type="text" class="form-control" id="lastName" name="lastName" required>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="email">Email</label>
                          <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="password">Password</label>
                          <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                      </div>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary btn-block">Create Admin</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
        <!-- End of Main Content -->

        <!-- Footer -->
        <?php include "Includes/footer.php"; ?>
        <!-- End of Footer -->
      
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>
