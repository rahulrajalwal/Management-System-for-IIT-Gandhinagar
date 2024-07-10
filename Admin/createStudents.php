<?php
error_reporting(0);
error_reporting(E_ALL); // Enable full error reporting for debugging
ini_set('display_errors', 1);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Save student data
if (isset($_POST['save'])) {
    $aadharNumber = $_POST['aadharNumber'];
    $surname = $_POST['surname'];
    $name = $_POST['name'];
    $secondName = $_POST['secondName'];
    $mobile = $_POST['mobile'];
    $alternateMobile = $_POST['alternateMobile'];
    $email = $_POST['email'];
    $currentAddress = $_POST['currentAddress'];
    $village = $_POST['village'];
    $permanentAddress = $_POST['permanentAddress'];
    $age = $_POST['age'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $education = $_POST['education'];
    $currentStatus = $_POST['currentStatus'];
    $familyWorkingStatus = $_POST['familyWorkingStatus'];
    $workExperience = $_POST['workExperience'];

    // Image file handling
    $livePhoto = $_FILES['livePhoto']['name'];
    $livePhotoTemp = $_FILES['livePhoto']['tmp_name'];

    $idFront = $_FILES['idFront']['name'];
    $idFrontTemp = $_FILES['idFront']['tmp_name'];

    $idBack = $_FILES['idBack']['name'];
    $idBackTemp = $_FILES['idBack']['tmp_name'];

    $deposited = $_POST['deposited'];

    // // Determine who added the student
    // $userRole = $_SESSION['userRole'];
    // $userId = $_SESSION['userId'];
    // $addedBy = '';

    // if ($userRole == 'Administrator') {
    //     $query = "SELECT emailAddress FROM tbladmin WHERE Id = '$userId'";
    //     $result = $conn->query($query);
    //     if ($result->num_rows > 0) {
    //         $row = $result->fetch_assoc();
    //         $addedBy = $row['emailAddress'];
    //     }
    // } elseif ($userRole == 'ClassTeacher') {
    //     $query = "SELECT emailAddress FROM tblclassteacher WHERE Id = '$userId'";
    //     $result = $conn->query($query);
    //     if ($result->num_rows > 0) {
    //         $row = $result->fetch_assoc();
    //         $addedBy = $row['emailAddress'];
    //     }
    // }

    // Insert query
    $query = "INSERT INTO tblstudents (aadharNumber, surname, name, secondName, mobile, alternateMobile, email, currentAddress, village, permanentAddress, age, dob, gender, education, currentStatus, familyWorkingStatus, workExperience, livePhoto, idFront, idBack, deposited, dateCreated) 
              VALUES ('$aadharNumber', '$surname', '$name', '$secondName', '$mobile', '$alternateMobile', '$email', '$currentAddress', '$village', '$permanentAddress', '$age', '$dob', '$gender', '$education', '$currentStatus', '$familyWorkingStatus', '$workExperience', '$livePhoto', '$idFront', '$idBack', '$deposited', NOW())";

    // Execute query and check result
    if (mysqli_query($conn, $query)) {
        $studentId = mysqli_insert_id($conn);
        
        move_uploaded_file($livePhotoTemp, '../uploads/' . $livePhoto);
        move_uploaded_file($idFrontTemp, '../uploads/' . $idFront);
        move_uploaded_file($idBackTemp, '../uploads/' . $idBack);

        // Insert student-class-arm relationships
        foreach ($_POST['classId'] as $index => $classId) {
            $classArmId = $_POST['classArmId'][$index];
            $query = "INSERT INTO tblstudent_class (student_id, class_id, class_arm_id) 
                      VALUES ('$studentId', '$classId', '$classArmId')";
            mysqli_query($conn, $query);
        }

        $statusMsg = "<div class='alert alert-success'>Student Created Successfully!</div>";
    } else {
        $statusMsg = "<div class='alert alert-danger'>An error occurred!</div>";
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
  <title>Add Student</title>
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
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script>
        var classFieldsCount = 1; // Initialize class fields count
        var maxClassFields = 13; // Maximum number of class fields allowed

        function addClassFields() {
            if (classFieldsCount >= maxClassFields) {
                alert("You can add a maximum of 13 classes.");
                return;
            }

            classFieldsCount++;

            var container = document.getElementById("classFieldsContainer");

            var classDiv = document.createElement("div");
            classDiv.className = "col-md-6 mb-3";
            classDiv.id = "classField_" + classFieldsCount;

            var classLabel = document.createElement("label");
            classLabel.className = "form-control-label";
            classLabel.innerHTML = "Class " + classFieldsCount + "<span class='text-danger ml-2'>*</span>";

            var classSelect = document.createElement("select");
            classSelect.className = "form-control";
            classSelect.name = "classId[]";
            classSelect.setAttribute("onChange", "classArmDropdown(this.value, " + classFieldsCount + ");");
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
            classArmDiv.className = "col-md-6 mb-3";
            classArmDiv.id = "classArmField_" + classFieldsCount;

            var classArmLabel = document.createElement("label");
            classArmLabel.className = "form-control-label";
            classArmLabel.innerHTML = "Batch " + classFieldsCount + "<span class='text-danger ml-2'>*</span>";

            var classArmSelect = document.createElement("select");
            classArmSelect.className = "form-control";
            classArmSelect.name = "classArmId[]";
            classArmSelect.id = "txtHint_" + classFieldsCount;
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

        function classArmDropdown(classId, fieldIndex) {
            $.ajax({
                url: 'getClassArms.php',
                method: 'POST',
                data: { classId: classId },
                success: function(response) {
                    $('#txtHint_' + fieldIndex).html(response);
                }
            });
        }
    </script>
    <style>
    .input-group .input-group-append .btn {
      border-left: 0;
    }

    .input-group .input-group-append .btn:first-child {
      border-right: 0;
    }

    .input-group .form-control {
      border-right: 0;
    }

    .input-group .input-group-append .btn .fa {
      pointer-events: none;
    }
  </style>
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
            <h1 class="h3 mb-0 text-gray-800">Add Student</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Add Student</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Add Student</h6>
                  <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post" enctype="multipart/form-data">
                    <div class="form-group row mb-3">
                      <div class="col-xl-4">
                        <label class="form-control-label">Aadhar Number<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="aadharNumber" required>
                      </div>
                      <div class="col-xl-4">
                        <label class="form-control-label">Surname<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="surname" required>
                      </div>
                      <div class="col-xl-4">
                        <label class="form-control-label">Name<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <div class="col-xl-4">
                        <label class="form-control-label">Second Name<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="secondName" required>
                      </div>
                      <div class="col-xl-4">
                        <label class="form-control-label">Mobile Number<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="mobile" required>
                      </div>
                      <div class="col-xl-4">
                        <label class="form-control-label">Alternate Mobile Number</label>
                        <input type="text" class="form-control" name="alternateMobile">
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <div class="col-xl-4">
                        <label class="form-control-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                      </div>
                      <div class="col-xl-4">
                        <label class="form-control-label">Current Address<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="currentAddress" required>
                      </div>
                      <div class="col-xl-4">
                          <label class="form-control-label">Village<span class="text-danger ml-2">*</span></label>
                          <div class="input-group mb-3">
                            <input type="text" class="form-control" id="villageSearch" placeholder="Search for a village...">
                            <div class="input-group-append">
                              <button class="btn btn-outline-secondary" type="button" id="searchIcon">
                                <i class="fa fa-search"></i>
                              </button>
                              <button class="btn btn-outline-secondary" type="button" id="addVillageIcon">
                                <i class="fa fa-plus"></i>
                              </button>
                            </div>
                          </div>
                          <select class="form-control" name="village" id="villageDropdown" required>
                            <!-- Options will be populated dynamically -->
                          </select>
                        </div>


                    </div>
                    <div class="form-group row mb-3">
                      <div class="col-xl-4">
                        <label class="form-control-label">Permanent Address<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="permanentAddress" required>
                      </div>
                      <div class="col-xl-4">
                        <label class="form-control-label">Age<span class="text-danger ml-2">*</span></label>
                        <input type="number" class="form-control" name="age" required>
                      </div>
                      <div class="col-xl-4">
                        <label class="form-control-label">Date of Birth<span class="text-danger ml-2">*</span></label>
                        <input type="date" class="form-control" name="dob" required>
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <div class="col-xl-4">
                        <label class="form-control-label">Gender<span class="text-danger ml-2">*</span></label>
                        <select class="form-control" name="gender" required>
                          <option value="">Select Gender</option>
                          <option value="Male">Male</option>
                          <option value="Female">Female</option>
                          <option value="Other">Other</option>
                        </select>
                      </div>
                      <div class="col-xl-4">
                        <label class="form-control-label">Education<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="education" required>
                      </div>
                      <div class="col-xl-4">
                      <label class="form-control-label">Current Status<span class="text-danger ml-2">*</span></label>
                        <select class="form-control" name="currentStatus" required>
                          <option value="">Select Status</option>
                          <option value="Searching for Job">Searching for Job</option>
                          <option value="Doing Job">Doing Job</option>
                          <option value="Studying">Studying</option>
                          <option value="Business">Business</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <div class="col-xl-4">
                        <label class="form-control-label">Family Working Status<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="familyWorkingStatus" required>
                      </div>
                      <div class="col-xl-4">
                        <label class="form-control-label">Work Experience<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="workExperience" required>
                      </div>
                      <div class="col-xl-4">
                        <label class="form-control-label">Live Photo<span class="text-danger ml-2">*</span></label>
                        <input type="file" class="form-control" name="livePhoto" required>
                      </div>
                    </div>
                    <div class="form-group row mb-3">
                      <div class="col-xl-4">
                        <label class="form-control-label">ID Front<span class="text-danger ml-2">*</span></label>
                        <input type="file" class="form-control" name="idFront" required>
                      </div>
                      <div class="col-xl-4">
                        <label class="form-control-label">ID Back<span class="text-danger ml-2">*</span></label>
                        <input type="file" class="form-control" name="idBack" required>
                      </div>
                      <div class="col-xl-4">
                      <label class="form-control-label">Deposited<span class="text-danger ml-2">*</span></label>
                        <select class="form-control" name="deposited" required>
                          <option value="">Select Option</option>
                          <option value="Deposit Received">Deposit Received</option>
                          <option value="ITI Student">ITI Student</option>
                        </select>
                      </div>
                    </div>
                    <div class="form-row mb-3" id="classFieldsContainer">
                        <div class="col-md-6 mb-3" id="classField_1">
                            <label class="form-control-label">Class 1<span class="text-danger ml-2">*</span></label>
                            <select class="form-control" name="classId[]" onChange="classArmDropdown(this.value, 1);" required>
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
                        <div class="col-md-6 mb-3" id="classArmField_1">
                            <label class="form-control-label">Batch 1<span class="text-danger ml-2">*</span></label>
                            <select class="form-control" name="classArmId[]" id="txtHint_1" required>
                                <option value="">--Select Batch--</option>
                            </select>
                            <button type="button" class="btn btn-danger btn-remove" onclick="removeClassFields(1);">Remove</button>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary" onclick="addClassFields();">Add Class</button>
                        </div>
                    </div>
                    <button type="submit" name="save" class="btn btn-primary mt-3">Submit</button>
                </form>
            </div>
        </div>
    </div>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script>
        $(document).ready(function() {
          // Load villages from the backend when the page loads
          loadVillages();
        
          // Filter the dropdown based on search input
          $('#villageSearch').on('keyup', function() {
            var searchValue = $(this).val().toLowerCase();
            $('#villageDropdown option').each(function() {
              var villageText = $(this).text().toLowerCase();
              $(this).toggle(villageText.indexOf(searchValue) > -1);
            });
          });
        
          // Add new village to the database and dropdown
          $('#addVillageIcon').on('click', function() {
            var newVillage = prompt("Please enter the new village name:");
            if (newVillage) {
              $.ajax({
                url: 'add_village.php',
                type: 'POST',
                data: { village: newVillage },
                dataType: 'json',
                success: function(response) {
                  if (response.success) {
                    $('#villageDropdown').append(new Option(newVillage, newVillage));
                    alert(newVillage + " has been added to the dropdown.");
                  } else {
                    alert('Error adding village: ' + response.message);
                  }
                },
                error: function(xhr, status, error) {
                  console.error('AJAX Error: ' + status + ': ' + error);
                  alert('Error adding village. Please check the console for more details.');
                }
              });
            }
          });
        
          function loadVillages() {
            $.ajax({
              url: 'get_villages.php',
              type: 'GET',
              dataType: 'json',
              success: function(response) {
                if (response.success) {
                  response.villages.forEach(function(village) {
                    $('#villageDropdown').append(new Option(village, village));
                  });
                } else {
                  alert('Error loading villages: ' + response.message);
                }
              },
              error: function(xhr, status, error) {
                console.error('AJAX Error: ' + status + ': ' + error);
                alert('Error loading villages. Please check the console for more details.');
              }
            });
          }
        });


      </script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
</script>
</body>
</html>
