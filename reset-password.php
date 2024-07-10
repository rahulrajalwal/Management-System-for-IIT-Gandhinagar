<?php
include 'Includes/dbcon.php';
session_start();

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Query to check if the token exists and has not expired
    $query = "SELECT * FROM password_resets WHERE token=? AND expire_time > NOW()";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "<div class='alert alert-danger' role='alert'>Prepare failed: (" . $conn->errno . ") " . $conn->error . "</div>";
        exit;
    }
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];
    } else {
        echo "<div class='alert alert-danger' role='alert'>Invalid or expired token.</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-danger' role='alert'>No token provided.</div>";
    exit;
}

if (isset($_POST['submit'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $hashed_password = md5($new_password); // Hash the new password with MD5

        // Update the password in the appropriate table
        $updateQuery1 = "UPDATE tbladmin SET password=? WHERE emailAddress=?";
        $updateQuery2 = "UPDATE tblclassteacher SET password=? WHERE emailAddress=?";

        $stmt1 = $conn->prepare($updateQuery1);
        $stmt1->bind_param("ss", $hashed_password, $email);
        $stmt2 = $conn->prepare($updateQuery2);
        $stmt2->bind_param("ss", $hashed_password, $email);

        $success1 = $stmt1->execute();
        $success2 = $stmt2->execute();

        if ($success1 || $success2) {
            // Delete the token after successful password reset
            $deleteQuery = "DELETE FROM password_resets WHERE email=?";
            $stmtDelete = $conn->prepare($deleteQuery);
            $stmtDelete->bind_param("s", $email);
            $stmtDelete->execute();

            echo "<div class='alert alert-success' role='alert'>Your password has been reset successfully.</div>";
        } else {
            echo "<div class='alert alert-danger' role='alert'>Failed to reset your password.</div>";
        }
    } else {
        echo "<div class='alert alert-danger' role='alert'>Passwords do not match.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Reset Password</title>
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-login">
  <div class="container-login">
    <div class="row justify-content-center">
      <div class="col-xl-10 col-lg-12 col-md-9">
        <div class="card shadow-sm my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="login-form">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Reset Password</h1>
                  </div>
                  <form class="user" method="POST" action="">
                    <div class="form-group">
                      <input type="password" class="form-control" name="new_password" placeholder="Enter New Password" required>
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control" name="confirm_password" placeholder="Confirm New Password" required>
                    </div>
                    <div class="form-group">
                      <input type="submit" class="btn btn-primary btn-block" name="submit" value="Reset Password">
                    </div>
                  </form>
                  <div class="text-center">
                    <a class="small" href="login.php">Back to Login</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
</body>
</html>
