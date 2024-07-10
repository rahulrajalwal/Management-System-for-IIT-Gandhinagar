<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Save functionality
if (isset($_POST['save'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $emailAddress = $_POST['emailAddress'];
    $phoneNo = $_POST['phoneNo'];
    $password = $_POST['password'];
    $dateCreated = date("Y-m-d");

    $query = mysqli_query($conn, "SELECT * FROM tblclassteacher WHERE emailAddress ='$emailAddress'");
    $ret = mysqli_fetch_array($query);

    $sampPass_2 = md5($password);

    if ($ret > 0) {
        $statusMsg = "<div class='alert alert-danger'>This Email Address Already Exists!</div>";
    } else {
        $query = mysqli_query($conn, "INSERT INTO tblclassteacher(firstName, lastName, emailAddress, password, phoneNo, dateCreated) 
                                      VALUES('$firstName', '$lastName', '$emailAddress', '$sampPass_2', '$phoneNo', '$dateCreated')");

        if ($query) {
            $teacherId = mysqli_insert_id($conn);

            foreach ($_POST['classId'] as $index => $classId) {
                $classArmId = $_POST['classArmId'][$index];
                $query = mysqli_query($conn, "INSERT INTO tblteacherclass(teacherId, classId, classArmId, dateCreated) 
                                              VALUES('$teacherId', '$classId', '$classArmId', '$dateCreated')");

                if ($query) {
                    $qu = mysqli_query($conn, "UPDATE tblclassarms SET isAssigned='1' WHERE Id ='$classArmId'");
                    if (!$qu) {
                        $statusMsg = "<div class='alert alert-danger'>An error occurred while updating class arm assignment!</div>";
                    }
                } else {
                    $statusMsg = "<div class='alert alert-danger'>An error occurred while inserting into tblteacherclass!</div>";
                }
            }

            if (!isset($statusMsg)) {
                $statusMsg = "<div class='alert alert-success'>Created Successfully!</div>";
            }
        } else {
            $statusMsg = "<div class='alert alert-danger'>An error occurred while inserting into tblclassteacher!</div>";
        }
    }
}

// Edit functionality
if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
    $Id = $_GET['Id'];

    // Fetch teacher's details from tblclassteacher
    $query = mysqli_query($conn, "SELECT * FROM tblclassteacher WHERE Id ='$Id'");
    $row = mysqli_fetch_array($query);

    // Fetch assigned classes and class arms from tblteacherclass
    $assignedClasses = [];
    $query_classes = mysqli_query($conn, "SELECT * FROM tblteacherclass WHERE teacherId='$Id'");
    while ($class_row = mysqli_fetch_assoc($query_classes)) {
        $assignedClasses[] = [
            'classId' => $class_row['classId'],
            'classArmId' => $class_row['classArmId']
        ];
    }

    if (isset($_POST['update'])) {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $emailAddress = $_POST['emailAddress'];
        $phoneNo = $_POST['phoneNo'];
        $dateCreated = date("Y-m-d");

        // Update teacher's details in tblclassteacher
        $update_query = mysqli_query($conn, "UPDATE tblclassteacher SET firstName='$firstName', lastName='$lastName', emailAddress='$emailAddress', phoneNo='$phoneNo' WHERE Id='$Id'");
        
        if ($update_query) {
            // Delete existing entries in tblteacherclass for the teacher
            $delete_query = mysqli_query($conn, "DELETE FROM tblteacherclass WHERE teacherId='$Id'");
            
            if ($delete_query) {
                // Insert new entries into tblteacherclass for the teacher's assigned classes
                foreach ($_POST['classId'] as $index => $classId) {
                    $classArmId = $_POST['classArmId'][$index];
                    $insert_query = mysqli_query($conn, "INSERT INTO tblteacherclass(teacherId, classId, classArmId, dateCreated) 
                                                        VALUES('$Id', '$classId', '$classArmId', '$dateCreated')");
                    
                    if ($insert_query) {
                        // Update tblclassarms to indicate assignment
                        $update_arm_query = mysqli_query($conn, "UPDATE tblclassarms SET isAssigned='1' WHERE Id ='$classArmId'");
                        if (!$update_arm_query) {
                            $statusMsg = "<div class='alert alert-danger'>An error occurred while updating class arm assignment!</div>";
                        }
                    } else {
                        $statusMsg = "<div class='alert alert-danger'>An error occurred while inserting into tblteacherclass!</div>";
                    }
                }

                if (!isset($statusMsg)) {
                    echo "<script type='text/javascript'>
                    window.location = ('createClassTeacher.php');
                    </script>";
                }
            } else {
                $statusMsg = "<div class='alert alert-danger'>An error occurred while deleting from tblteacherclass!</div>";
            }
        } else {
            $statusMsg = "<div class='alert alert-danger'>An error occurred while updating tblclassteacher!</div>";
        }
    }
}


// Delete functionality
if (isset($_GET['Id']) && isset($_GET['classArmId']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $Id = $_GET['Id'];
    $classArmId = $_GET['classArmId'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM tblclassteacher WHERE Id = ?");
    $stmt->bind_param("i", $Id);
    if ($stmt->execute()) {
        $stmt->close();

        // Delete from tblteacherclass
        $stmt = $conn->prepare("DELETE FROM tblteacherclass WHERE teacherId = ?");
        $stmt->bind_param("i", $Id);
        if ($stmt->execute()) {
            $stmt->close();

            // Update tblclassarms to set isAssigned to 0 for the deleted classArmId
            $stmt = $conn->prepare("UPDATE tblclassarms SET isAssigned = '0' WHERE Id = ?");
            $stmt->bind_param("i", $classArmId);
            if ($stmt->execute()) {
                $stmt->close();
                echo "<script type='text/javascript'>
                    window.location = ('createClassTeacher.php');
                    </script>";
            } else {
                $stmt->close();
                $statusMsg = "<div class='alert alert-danger'>An error occurred while updating class arm assignment: " . $conn->error . "</div>";
            }
        } else {
            $stmt->close();
            $statusMsg = "<div class='alert alert-danger'>An error occurred while deleting from tblteacherclass: " . $conn->error . "</div>";
        }
    } else {
        $stmt->close();
        $statusMsg = "<div class='alert alert-danger'>An error occurred while deleting from tblclassteacher: " . $conn->error . "</div>";
    }

    if (isset($statusMsg)) {
        echo $statusMsg;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Class Teachers</title>
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
        document.getElementById("txtHint_" + index).innerHTML = "<option>--Select Batch--</option>";
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
      defaultOption.innerHTML = "--Select Course--";
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
      classArmLabel.innerHTML = "Batch " + classFieldsCount + "<span class='text-danger ml-2'>*</span>";

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
            <h1 class="h3 mb-0 text-gray-800">Create Class Teachers</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Create Class Teachers</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary"><?php echo isset($Id) ? 'Edit' : 'Create'; ?> Class Teachers</h6>
                  <?php if (isset($statusMsg)) { echo $statusMsg; } ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                      <div class="col-xl-6 mb-3">
                        <label class="form-control-label">First Name<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="firstName" value="<?php echo isset($row['firstName']) ? $row['firstName'] : ''; ?>" required>
                      </div>
                      <div class="col-xl-6 mb-3">
                        <label class="form-control-label">Last Name<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="lastName" value="<?php echo isset($row['lastName']) ? $row['lastName'] : ''; ?>" required>
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <div class="col-xl-6 mb-3">
                        <label class="form-control-label">Email<span class="text-danger ml-2">*</span></label>
                        <input type="email" class="form-control" name="emailAddress" value="<?php echo isset($row['emailAddress']) ? $row['emailAddress'] : ''; ?>" required>
                      </div>
                      <div class="col-xl-6 mb-3">
                        <label class="form-control-label">Phone Number<span class="text-danger ml-2">*</span></label>
                        <input type="tel" class="form-control" name="phoneNo" value="<?php echo isset($row['phoneNo']) ? $row['phoneNo'] : ''; ?>" required>
                      </div>
                    </div>
                    <?php if (!isset($_GET['Id']) || !isset($_GET['action']) || $_GET['action'] != "edit") { ?>
                    <div class="form-group row mb-3">
                        <div class="col-xl-6 mb-3">
                            <label class="form-control-label">Password<span class="text-danger ml-2">*</span></label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </div>
                    <?php } ?>

                    <div id="classFieldsContainer">
                      <?php
                        // Check if editing existing teacher or creating new one
                        if (isset($assignedClasses) && !empty($assignedClasses)) {
                          foreach ($assignedClasses as $index => $assignedClass) {
                      ?>
                      <div class="form-group row mb-3">
                        <div class="col-xl-6 mb-3">
                          <label class="form-control-label">Class <?php echo ($index + 1); ?><span class="text-danger ml-2">*</span></label>
                          <select class="form-control" name="classId[]" onChange="classArmDropdown(this.value, <?php echo $index; ?>);" required>
                            <option value="">--Select Course--</option>
                            <?php
                              $qry = "SELECT * FROM tblclass ORDER BY className ASC";
                              $result = $conn->query($qry);
                              if ($result->num_rows > 0) {
                                while ($rows = $result->fetch_assoc()) {
                                  $selected = ($assignedClass['classId'] == $rows['Id']) ? 'selected' : '';
                            ?>
                            <option value="<?php echo $rows['Id']; ?>" <?php echo $selected; ?>><?php echo $rows['className']; ?></option>
                            <?php
                                }
                              }
                            ?>
                          </select>
                        </div>

                        <div class="col-xl-6 mb-3">
                          <label class="form-control-label">Class Arm <?php echo ($index + 1); ?><span class="text-danger ml-2">*</span></label>
                          <select class="form-control" name="classArmId[]" id="txtHint_<?php echo $index; ?>" required>
                            <option value="">--Select Batch--</option>
                            <?php
                              $classId = $assignedClass['classId'];
                              $qry_classArms = "SELECT * FROM tblclassarms WHERE classId='$classId' ORDER BY classArmName ASC";
                              $result_classArms = $conn->query($qry_classArms);
                              if ($result_classArms->num_rows > 0) {
                                while ($rows_classArms = $result_classArms->fetch_assoc()) {
                                  $selected = ($assignedClass['classArmId'] == $rows_classArms['Id']) ? 'selected' : '';
                            ?>
                            <option value="<?php echo $rows_classArms['Id']; ?>" <?php echo $selected; ?>><?php echo $rows_classArms['classArmName']; ?></option>
                            <?php
                                }
                              }
                            ?>
                          </select>
                        </div>
                      </div>
                      <?php
                          }
                        } else {
                      ?>
                      <div class="form-group row mb-3">
                        <div class="col-xl-6 mb-3">
                          <label class="form-control-label">Course 1<span class="text-danger ml-2">*</span></label>
                          <select class="form-control" name="classId[]" onChange="classArmDropdown(this.value, 0);" required>
                            <option value="">--Select Course--</option>
                            <?php
                              $qry = "SELECT * FROM tblclass ORDER BY className ASC";
                              $result = $conn->query($qry);
                              if ($result->num_rows > 0) {
                                while ($rows = $result->fetch_assoc()) {
                            ?>
                            <option value="<?php echo $rows['Id']; ?>"><?php echo $rows['className']; ?></option>
                            <?php
                                }
                              }
                            ?>
                          </select>
                        </div>

                        <div class="col-xl-6 mb-3">
                          <label class="form-control-label">Batch 1<span class="text-danger ml-2">*</span></label>
                          <select class="form-control" name="classArmId[]" id="txtHint_0" required>
                            <option value="">--Select Batch--</option>
                          </select>
                        </div>
                      </div>
                      <?php
                        }
                      ?>
                    </div>

                    <div class="form-group row mb-3">
                      <div class="col-12">
                        <button type="button" class="btn btn-primary" onclick="addClassFields()">Add Another Course</button>
                      </div>
                    </div>

                    <?php if (isset($Id)) { ?>
                      <button type="submit" name="update" class="btn btn-primary">Update</button>
                    <?php } else { ?>
                      <button type="submit" name="save" class="btn btn-primary">Save</button>
                    <?php } ?>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">All Class Teachers</h6>
                </div>
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Date Created</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $query = mysqli_query($conn, "SELECT * FROM tblclassteacher");
                        $cnt = 1;
                        while ($row = mysqli_fetch_array($query)) {
                      ?>
                      <tr>
                        <td><?php echo $cnt; ?></td>
                        <td><?php echo $row['firstName'] . " " . $row['lastName']; ?></td>
                        <td><?php echo $row['emailAddress']; ?></td>
                        <td><?php echo $row['phoneNo']; ?></td>
                        <td><?php echo $row['dateCreated']; ?></td>
                        <td>
                          <a href="?action=edit&Id=<?php echo $row['Id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                          <a href="?action=delete&Id=<?php echo $row['Id']; ?>&classArmId=<?php echo $row['classArmId']; ?>" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                      </tr>
                      <?php $cnt++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
      <?php include "Includes/footer.php"; ?>
    </div>
  </div>
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="../js/ruang-admin.min.js"></script>
</body>
</html>


