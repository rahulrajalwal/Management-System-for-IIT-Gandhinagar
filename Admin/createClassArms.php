<?php
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

//------------------------SAVE and UPDATE--------------------------------------------------

if(isset($_POST['save'])){
    $classId = $_POST['classId'];
    $classArmName = $_POST['classArmName'];
    $startDate = date("Y-m-d", strtotime($_POST['startDate']));
    $endDate = date("Y-m-d", strtotime($_POST['endDate']));

    $query = mysqli_query($conn, "select * from tblclassarms where classArmName ='$classArmName' and classId = '$classId'");
    $ret = mysqli_fetch_array($query);

    if($ret > 0){ 
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Class Arm Already Exists!</div>";
    } else {
        $query = mysqli_query($conn, "insert into tblclassarms(classId, classArmName, startDate, endDate, isAssigned) values('$classId', '$classArmName', '$startDate', '$endDate', '0')");

        if ($query) {
            $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Created Successfully!</div>";
        } else {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
        }
    }
}

if(isset($_POST['update'])){
    $Id = $_POST['Id'];
    $classId = $_POST['classId'];
    $classArmName = $_POST['classArmName'];
    $startDate = date("Y-m-d", strtotime($_POST['startDate']));
    $endDate = date("Y-m-d", strtotime($_POST['endDate']));

    $query = mysqli_query($conn, "update tblclassarms set classId = '$classId', classArmName='$classArmName', startDate='$startDate', endDate='$endDate' where Id='$Id'");

    if ($query) {
        echo "<script type='text/javascript'>
        window.location = 'createClassArms.php';
        </script>"; 
    } else {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
    }
}

//---------------------------------------EDIT-------------------------------------------------------------

$editMode = false;
if (isset($_GET['action']) && $_GET['action'] == "edit" && isset($_GET['Id'])) {
    $editMode = true;
    $Id = $_GET['Id'];
    $query = mysqli_query($conn, "select * from tblclassarms where Id ='$Id'");
    $row = mysqli_fetch_array($query);
}

//--------------------------------TOGGLE STATUS------------------------------------------------------------------

if (isset($_GET['action']) && $_GET['action'] == "toggle" && isset($_GET['Id']) && isset($_GET['status'])) {
    $Id = $_GET['Id'];
    $status = $_GET['status'];

    $newStatus = $status == '1' ? '0' : '1';
    $query = mysqli_query($conn, "UPDATE tblclassarms SET isAssigned = '$newStatus' WHERE Id='$Id'");

    if ($query) {
        echo "<script type='text/javascript'>
        window.location = 'createClassArms.php';
        </script>";  
    } else {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>"; 
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
  <title>Create Batch</title>
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
            <h1 class="h3 mb-0 text-gray-800">Create Batch</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Create Batch</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary"><?php echo $editMode ? 'Update Class Batch' : 'Create Batch'; ?></h6>
                  <?php echo isset($statusMsg) ? $statusMsg : ''; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Select Course<span class="text-danger ml-2">*</span></label>
                        <?php
                        $qry= "SELECT * FROM tblclass ORDER BY className ASC";
                        $result = $conn->query($qry);
                        $num = $result->num_rows;
                        ?>
                        <select required name="classId" class="form-control mb-3">
                          <option value="">--Select Course--</option>
                          <?php
                          if ($num > 0){
                            while ($rows = $result->fetch_assoc()){
                              $selected = isset($row['classId']) && $row['classId'] == $rows['Id'] ? 'selected' : '';
                              echo '<option value="'.$rows['Id'].'" '.$selected.'>'.$rows['className'].'</option>';
                            }
                          }
                          ?>
                        </select>
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Batch Name<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" name="classArmName" value="<?php echo isset($row['classArmName']) ? $row['classArmName'] : ''; ?>" id="exampleInputFirstName" placeholder="Batch Name" required>
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Start Date<span class="text-danger ml-2">*</span></label>
                        <input type="date" class="form-control" name="startDate" value="<?php echo isset($row['startDate']) ? date("Y-m-d", strtotime($row['startDate'])) : ''; ?>" required>
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">End Date<span class="text-danger ml-2">*</span></label>
                        <input type="date" class="form-control" name="endDate" value="<?php echo isset($row['endDate']) ? date("Y-m-d", strtotime($row['endDate'])) : ''; ?>" required>
                      </div>
                    </div>

                    <?php if ($editMode): ?>
                      <input type="hidden" name="Id" value="<?php echo $Id; ?>">
                      <button type="submit" name="update" class="btn btn-warning">Update</button>
                    <?php else: ?>
                      <button type="submit" name="save" class="btn btn-primary">Save</button>
                    <?php endif; ?>
                  </form>
                </div>
              </div>

              <!-- Table with Data -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">All Batches</h6>
                </div>
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush table-hover" id="dataTableHover">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>Course Name</th>
                        <th>Batch Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Edit</th>
                        <th>Toggle Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "SELECT tblclassarms.Id, tblclassarms.isAssigned, tblclass.className, tblclassarms.classArmName, tblclassarms.startDate, tblclassarms.endDate
                                FROM tblclassarms
                                INNER JOIN tblclass ON tblclass.Id = tblclassarms.classId";
                      $rs = $conn->query($query);
                      if ($rs->num_rows > 0) {
                        $sn = 0;
                        while ($rows = $rs->fetch_assoc()) {
                          $sn++;
                          $status = $rows['isAssigned'] == '1' ? "Active" : "Inactive";
                          $currentDate = date('Y-m-d');
                          if ($currentDate > $rows['endDate']) {
                            $status = "Inactive";
                            mysqli_query($conn, "UPDATE tblclassarms SET isAssigned='0' WHERE Id={$rows['Id']}");
                          }
                          echo "<tr>
                            <td>{$sn}</td>
                            <td>{$rows['className']}</td>
                            <td>{$rows['classArmName']}</td>
                            <td>" . date("Y-m-d", strtotime($rows['startDate'])) . "</td>
                            <td>" . date("Y-m-d", strtotime($rows['endDate'])) . "</td>
                            <td>{$status}</td>
                            <td><a href='?action=edit&Id={$rows['Id']}'><i class='fas fa-fw fa-edit'></i>Edit</a></td>
                            <td><a href='?action=toggle&Id={$rows['Id']}&status={$rows['isAssigned']}'><i class='fas fa-fw fa-toggle-" . ($rows['isAssigned'] == '1' ? 'on' : 'off') . "'></i>{$status}</a></td>
                          </tr>";
                        }
                      } else {
                        echo "<tr><td colspan='8'><div class='alert alert-danger' role='alert'>No Record Found!</div></td></tr>";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <!-- End Table with Data -->
            </div>
          </div>
          <!---Container Fluid-->
        </div>
        <!-- Footer -->
        <?php include "Includes/footer.php";?>
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

    <!-- Page level custom scripts -->
    <script>
      $(document).ready(function () {
        $('#dataTable').DataTable(); // ID From dataTable 
        $('#dataTableHover').DataTable(); // ID From dataTable with Hover
      });

      // Function to format date as Y-m-d
      function formatDate(dateStr) {
        const date = new Date(dateStr);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}-${month}-${year}`;
      }

      // Format dates on load
      document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.querySelector('input[name="startDate"]');
        const endDateInput = document.querySelector('input[name="endDate"]');
        if (startDateInput && startDateInput.value) {
          startDateInput.value = formatDate(startDateInput.value);
        }
        if (endDateInput && endDateInput.value) {
          endDateInput.value = formatDate(endDateInput.value);
        }
      });
    </script>
  </body>
</html>
