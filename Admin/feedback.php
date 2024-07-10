<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Initialize variables
$row = [];
$assignedClasses = [];

// Fetch student data for editing
if (isset($_GET['id'])) {
    $Id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM tblstudents WHERE Id = ?");
    $stmt->bind_param("i", $Id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        echo "<div class='alert alert-danger'>Invalid Student ID</div>";
        exit;
    }

    // Fetch the assigned classes and class arms
    $stmt = $conn->prepare("SELECT * FROM tblstudent_class WHERE student_id = ?");
    $stmt->bind_param("i", $Id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($classRow = $result->fetch_assoc()) {
        $assignedClasses[] = $classRow;
    }
} else {
    echo "<div class='alert alert-danger'>No Student ID provided</div>";
    exit;
}

// Handle feedback submission
if (isset($_POST['submitFeedback'])) {
    $classId = $_POST['classId'];
    $classArmId = $_POST['classArmId'];
    $feedbacks = $_POST['feedback'];

    $stmt = $conn->prepare("UPDATE tblstudent_class SET feedback1=?, feedback2=?, feedback3=?, feedback4=?, feedback5=?, feedback6=?, feedback7=?, feedback8=?, feedback9=?, feedback10=? WHERE student_id=? AND class_id=? AND class_arm_id=?");

    $feedbackValues = array_pad($feedbacks, 10, null); // Ensure there are 10 feedbacks
    $stmt->bind_param("ssssssssssiii", $feedbackValues[0], $feedbackValues[1], $feedbackValues[2], $feedbackValues[3], $feedbackValues[4], $feedbackValues[5], $feedbackValues[6], $feedbackValues[7], $feedbackValues[8], $feedbackValues[9], $Id, $classId, $classArmId);
    $stmt->execute();

    echo "<div class='alert alert-success'>Feedback submitted successfully</div>";
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
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <style>
    .form-control-label {
      font-weight: bold;
    }
    .btn-remove {
      margin-top: 32px;
    }
  </style>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script>
    var feedbackCount = 1;
    const maxFeedbackCount = 10;

    function addFeedbackField() {
      if (feedbackCount >= maxFeedbackCount) {
        alert("You can add a maximum of 10 feedbacks.");
        return;
      }

      feedbackCount++;

      var container = document.getElementById("feedbackFieldsContainer");

      var feedbackDiv = document.createElement("div");
      feedbackDiv.className = "col-lg-12 mb-3";
      feedbackDiv.id = "feedbackField_" + feedbackCount;

      var feedbackLabel = document.createElement("label");
      feedbackLabel.className = "form-control-label";
      feedbackLabel.innerHTML = "Feedback " + feedbackCount;

      var feedbackInput = document.createElement("textarea");
      feedbackInput.className = "form-control";
      feedbackInput.name = "feedback[]";
      feedbackInput.required = true;

      var removeButton = document.createElement("button");
      removeButton.type = "button";
      removeButton.className = "btn btn-danger btn-remove";
      removeButton.innerHTML = "Remove";
      removeButton.setAttribute("onclick", "removeFeedbackField(" + feedbackCount + ");");

      feedbackDiv.appendChild(feedbackLabel);
      feedbackDiv.appendChild(feedbackInput);
      feedbackDiv.appendChild(removeButton);

      container.appendChild(feedbackDiv);
    }

    function removeFeedbackField(id) {
      var feedbackDiv = document.getElementById("feedbackField_" + id);
      if (feedbackDiv) {
        feedbackDiv.remove();
        feedbackCount--;
      }
    }

    function fetchClassArms(classId) {
      $.ajax({
        url: 'fetch_class_arm.php',
        type: 'POST',
        data: { classId: classId },
        success: function(response) {
          $('#classArmSelect').html(response);
        }
      });
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
            <h1 class="h3 mb-0 text-gray-800">Feedback</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Feedback</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Provide Feedback</h6>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label class="form-control-label">Student Name:</label>
                          <input type="text" class="form-control" value="<?php echo $row['name']; ?>" disabled>
                        </div>
                      </div>

                      <div class="col-lg-6">
                        <div class="form-group">
                          <label class="form-control-label">Aadhaar Number:</label>
                          <input type="text" class="form-control" value="<?php echo $row['aadharNumber']; ?>" disabled>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label class="form-control-label">Class<span class="text-danger ml-2">*</span></label>
                          <select class="form-control" name="classId" onchange="fetchClassArms(this.value)" required>
                            <option value="">--Select Class--</option>
                            <?php
                              foreach ($assignedClasses as $assignedClass) {
                                $classId = $assignedClass['class_id'];
                                $stmt = $conn->prepare("SELECT * FROM tblclass WHERE Id = ?");
                                $stmt->bind_param("i", $classId);
                                $stmt->execute();
                                $classResult = $stmt->get_result();
                                
                                if ($classResult->num_rows > 0) {
                                    $classRow = $classResult->fetch_assoc();
                                    echo '<option value="'.$classRow['Id'].'">'.$classRow['className'].'</option>';
                                }
                              }
                            ?>
                          </select>
                        </div>
                      </div>

                      <div class="col-lg-6">
                        <div class="form-group">
                          <label class="form-control-label">Class Arm<span class="text-danger ml-2">*</span></label>
                          <select class="form-control" name="classArmId" id="classArmSelect" required>
                            <option value="">--Select Class Arm--</option>
                          </select>
                        </div>
                      </div>
                    </div>

                    <div id="feedbackFieldsContainer" class="row">
                      <div class="col-lg-12 mb-3" id="feedbackField_1">
                        <label class="form-control-label">Feedback 1</label>
                        <textarea class="form-control" name="feedback[]" required></textarea>
                        <button type="button" class="btn btn-danger btn-remove" onclick="removeFeedbackField(1);">Remove</button>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-lg-12 mb-3">
                        <button type="button" class="btn btn-primary" onclick="addFeedbackField();">Add Feedback</button>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-lg-12">
                        <button type="submit" name="submitFeedback" class="btn btn-success">Submit Feedback</button>
                      </div>
                    </div>
                  </form>
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
  
</body>

</html>
