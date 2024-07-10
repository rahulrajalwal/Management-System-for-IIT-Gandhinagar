<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include 'Includes/dbcon.php';
include 'mailer.php';
session_start();

if (isset($_POST['submit'])) {
    $email = $_POST['email'];

    // Check if email exists in the database
    $query = "SELECT emailAddress FROM tbladmin WHERE emailAddress = ?
              UNION
              SELECT emailAddress FROM tblclassteacher WHERE emailAddress = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a unique reset token
        $token = bin2hex(random_bytes(50));
        $expire_time = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Insert token into the database
        $insertQuery = $conn->prepare("INSERT INTO password_resets (email, token, expire_time) VALUES (?, ?, ?)");
        $insertQuery->bind_param("sss", $email, $token, $expire_time);
        $insertQuery->execute();

        // Send email with reset link
        $subject = 'Password Reset Request';
        $body = 'Click on this link to reset your password: <a href="http://att.neeviitgn.com/reset-password.php?token='.$token.'">Reset Password</a>';
        $altBody = 'Click on this link to reset your password: http://att.neeviitgn.com/reset-password.php?token='.$token;

        if (sendMail($email, $subject, $body, $altBody)) {
            echo "<div class='alert alert-success' role='alert'>Password reset link has been sent to your email.</div>";
        } else {
            echo "<div class='alert alert-danger' role='alert'>Failed to send the email.</div>";
        }
    } else {
        echo "<div class='alert alert-danger' role='alert'>Email address not found.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Forgot Password</title>
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
                    <h1 class="h4 text-gray-900 mb-4">Forgot Password</h1>
                  </div>
                  <form class="user" method="POST" action="">
                    <div class="form-group">
                      <input type="email" class="form-control" name="email" placeholder="Enter Email Address" required>
                    </div>
                    <div class="form-group">
                      <input type="submit" class="btn btn-primary btn-block" name="submit" value="Reset Password">
                    </div>
                  </form>
                  <div class="text-center">
                    <a class="small" href="index.php">Back to Login</a>
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
