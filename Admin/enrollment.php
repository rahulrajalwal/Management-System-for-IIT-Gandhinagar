<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Initialize $row to an empty array
$row = [];
$assignedClasses = [];

// Fetch student data for editing
if (isset($_GET['id'])) {
    $Id = $_GET['id'];

    $query = mysqli_query($conn, "SELECT * FROM tblstudents WHERE Id = '$Id'");
    $row = mysqli_fetch_array($query);

    if (!$row) {
        echo "<div class='alert alert-danger'>Invalid Student ID</div>";
        exit;
    }

    // Fetch the assigned classes and class arms
    $classQuery = mysqli_query($conn, "SELECT * FROM tblstudent_class WHERE student_id = '$Id'");
    while ($classRow = mysqli_fetch_assoc($classQuery)) {
        $assignedClasses[] = $classRow;
    }
} else {
    echo "<div class='alert alert-danger'>No Student ID provided</div>";
    exit;
}

// Update student data
if (isset($_POST['update'])) {
    $Id = $_POST['Id'];
    $aadharNumber = $_POST['aadharNumber'];
    $name = $_POST['name'];
   

    // Update query
    $update_query = "UPDATE tblstudents SET aadharNumber = '$aadharNumber',
    name = '$name' WHERE Id = '$Id'";
    
    if (mysqli_query($conn, $update_query)) {
    // Delete existing entries in tblstudent_class for the student
    $delete_query = mysqli_query($conn, "DELETE FROM tblstudent_class WHERE student_id='$Id'");
    if ($delete_query) {
        // Insert new entries into tblstudent_class for the student's assigned classes
        foreach ($_POST['classId'] as $index => $classId) {
            $classArmId = $_POST['classArmId'][$index];
            $insert_query = mysqli_query($conn, "INSERT INTO tblstudent_class(student_id, class_id, class_arm_id) VALUES('$Id', '$classId', '$classArmId')");
            if (!$insert_query) {
                $statusMsg = "<div class='alert alert-danger'>An error occurred while inserting into tblstudent_class!</div>";
                break;
            }
        }
        if (!isset($statusMsg)) {
            // Set success message
            $statusMsg = "<div class='alert alert-success'>Enrolled successfully.</div>";
        }
    } else {
        $statusMsg = "<div class='alert alert-danger'>An error occurred while deleting from tblstudent_class!</div>";
    }
} else {
    $statusMsg = "<div class='alert alert-danger'>An error occurred while updating tblstudents!</div>";
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
  <script>
    var classFieldsCount = <?php echo isset($assignedClasses) ? count($assignedClasses) : 1; ?>; 
    const maxClassFields = 13; 

    function classArmDropdown(str, index) {
      if (str === "") {
        document.getElementById("txtHint_" + index).innerHTML = "<option>--Select Class Arm--</option>";
        return;
      } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
          if (this.readyState === 4 && this.status === 200) {
            document.getElementById("txtHint_" + index).innerHTML = this.responseText;
          }
        };
        xmlhttp.open("GET", "ajaxClassArms.php?cid=" + str, true);
        xmlhttp.send();
      }
    }

    function addClassFields() {
      if (classFieldsCount >= maxClassFields) {
        alert("You can add a maximum of 13 classes.");
        return;
      }

      classFieldsCount++;

      var container = document.getElementById("classFieldsContainer");

      var classDiv = document.createElement("div");
      classDiv.className = "col-xl-6 mb-3";
      classDiv.id = "classField_" + classFieldsCount;

      var classLabel = document.createElement("label");
      classLabel.className = "form-control-label";
      classLabel.innerHTML = "Class " + classFieldsCount + "<span class='text-danger ml-2'>*</span>";

      var classSelect = document.createElement("select");
      classSelect.className = "form-control";
      classSelect.name = "classId[]";
      classSelect.setAttribute("onChange", "classArmDropdown(this.value, " + (classFieldsCount - 1) + ");");
      classSelect.required = true;

      var defaultOption = document.createElement("option");
      defaultOption.value = "";
      defaultOption.innerHTML = "--Select Class--";
      classSelect.appendChild(defaultOption);

      <?php
        $qry = "SELECT * FROM tblclass ORDER BY className ASC";
        $result = $conn->query($qry);
        if ($result->num_rows > 0) {
          while ($rows = $result->fetch_assoc()) {
      ?>
      var option = document.createElement("option");
      option.value = "<?php echo $rows['Id']; ?>";
      option.innerHTML = "<?php echo $rows['className']; ?>";
      classSelect.appendChild(option);
      <?php
          }
        }
      ?>

      classDiv.appendChild(classLabel);
      classDiv.appendChild(classSelect);

      var classArmDiv = document.createElement("div");
      classArmDiv.className = "col-xl-6 mb-3";
      classArmDiv.id = "classArmField_" + classFieldsCount;

      var classArmLabel = document.createElement("label");
      classArmLabel.className = "form-control-label";
      classArmLabel.innerHTML = "Class Arm " + classFieldsCount + "<span class='text-danger ml-2'>*</span>";

      var classArmSelect = document.createElement("select");
      classArmSelect.className = "form-control";
      classArmSelect.name = "classArmId[]";
      classArmSelect.id = "txtHint_" + (classFieldsCount - 1);
      classArmSelect.required = true;

      var removeButton = document.createElement("button");
      removeButton.type = "button";
      removeButton.className = "btn btn-danger btn-remove";
      removeButton.innerHTML = "Remove";
      removeButton.setAttribute("onclick", "removeClassFields(" + classFieldsCount + ");");

      classArmDiv.appendChild(classArmLabel);
      classArmDiv.appendChild(classArmSelect);
      classArmDiv.appendChild(removeButton);

      container.appendChild(classDiv);
      container.appendChild(classArmDiv);
    }

    function removeClassFields(id) {
      var classDiv = document.getElementById("classField_" + id);
      var classArmDiv = document.getElementById("classArmField_" + id);

      if (classDiv && classArmDiv) {
        classDiv.remove();
        classArmDiv.remove();
        classFieldsCount--;
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
            <h1 class="h3 mb-0 text-gray-800">Enrollment</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Enrollment</h6>
                </div>
                <div class="card-body">
                  <?php if (isset($statusMsg)) echo $statusMsg; ?>
                  <form method="post">
                    <input type="hidden" name="Id" value="<?php echo $row['Id']; ?>" />
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label class="form-control-label">ID Number<span class="text-danger ml-2">*</span></label>
                          <input type="text" class="form-control" name="aadharNumber" value="<?php echo isset($row['aadharNumber']) ? $row['aadharNumber'] : ''; ?>"  disabled>
                        </div>
                      </div>

                      
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label class="form-control-label">Name<span class="text-danger ml-2">*</span></label>
                          <input type="text" class="form-control" name="name" value="<?php echo isset($row['name']) ? $row['name'] : ''; ?>"  disabled>
                        </div>
                      </div>
                    </div>

                      

                    <div id="classFieldsContainer" class="row">
                      <?php if (!empty($assignedClasses)) { ?>
                        <?php foreach ($assignedClasses as $index => $assignedClass) { ?>
                          <div class="col-xl-6 mb-3" id="classField_<?php echo $index + 1; ?>">
                            <label class="form-control-label">Class <?php echo $index + 1; ?><span class="text-danger ml-2">*</span></label>
                            <select class="form-control" name="classId[]" onChange="classArmDropdown(this.value, <?php echo $index; ?>);" required>
                              <option value="">--Select Class--</option>
                              <?php
                                $qry = "SELECT * FROM tblclass ORDER BY className ASC";
                                $result = $conn->query($qry);
                                if ($result->num_rows > 0) {
                                  while ($rows = $result->fetch_assoc()) {
                                    $selected = $rows['Id'] == $assignedClass['class_id'] ? 'selected' : '';
                                    echo '<option value="'.$rows['Id'].'" '.$selected.'>'.$rows['className'].'</option>';
                                  }
                                }
                              ?>
                            </select>
                          </div>

                          <div class="col-xl-6 mb-3" id="classArmField_<?php echo $index + 1; ?>">
                            <label class="form-control-label">Class Arm <?php echo $index + 1; ?><span class="text-danger ml-2">*</span></label>
                            <select class="form-control" name="classArmId[]" id="txtHint_<?php echo $index; ?>" required>
                              <option value="">--Select Class Arm--</option>
                              <?php
                                $qry = "SELECT * FROM tblclassarms WHERE classId = '".$assignedClass['class_id']."' ORDER BY classArmName ASC";
                                $result = $conn->query($qry);
                                if ($result->num_rows > 0) {
                                  while ($rows = $result->fetch_assoc()) {
                                    $selected = $rows['Id'] == $assignedClass['class_arm_id'] ? 'selected' : '';
                                    echo '<option value="'.$rows['Id'].'" '.$selected.'>'.$rows['classArmName'].'</option>';
                                  }
                                }
                              ?>
                            </select>
                            <button type="button" class="btn btn-danger btn-remove" onclick="removeClassFields(<?php echo $index + 1; ?>);">Remove</button>
                          </div>
                        <?php } ?>
                      <?php } else { ?>
                        <div class="col-xl-6 mb-3" id="classField_1">
                          <label class="form-control-label">Class 1<span class="text-danger ml-2">*</span></label>
                          <select class="form-control" name="classId[]" onChange="classArmDropdown(this.value, 0);" required>
                            <option value="">--Select Class--</option>
                            <?php
                              $qry = "SELECT * FROM tblclass ORDER BY className ASC";
                              $result = $conn->query($qry);
                              if ($result->num_rows > 0) {
                                while ($rows = $result->fetch_assoc()) {
                                  echo '<option value="'.$rows['Id'].'">'.$rows['className'].'</option>';
                                }
                              }
                            ?>
                          </select>
                        </div>

                        <div class="col-xl-6 mb-3" id="classArmField_1">
                          <label class="form-control-label">Class Arm 1<span class="text-danger ml-2">*</span></label>
                          <select class="form-control" name="classArmId[]" id="txtHint_0" required>
                            <option value="">--Select Class Arm--</option>
                          </select>
                          <button type="button" class="btn btn-danger btn-remove" onclick="removeClassFields(1);">Remove</button>
                        </div>
                      <?php } ?>
                    </div>

                    <div class="row">
                      <div class="col-lg-12 mb-3">
                        <button type="button" class="btn btn-primary" onclick="addClassFields();">Add Class</button>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-lg-12">
                        <button type="submit" name="update" class="btn btn-success">Update Student</button>
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
