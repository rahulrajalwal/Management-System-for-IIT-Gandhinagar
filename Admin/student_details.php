<?php
session_start();
include '../Includes/dbcon.php'; // Include your database connection file here
// Function to extract Google Drive File ID
function extractGoogleDriveFileId($url) {
    $patterns = [
        '/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/',
        '/drive\.google\.com\/open\?id=([a-zA-Z0-9_-]+)/'
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
    }

    return null;
}

// Function to download Google Drive file
function downloadGoogleDriveFile($fileId, $destinationPath) {
    $downloadUrl = "https://drive.google.com/uc?export=download&id=" . $fileId;
    $fileContent = file_get_contents($downloadUrl);

    if ($fileContent === FALSE) {
        return false;
    }

    file_put_contents($destinationPath, $fileContent);
    return true;
}

// Fetch user details based on the student ID from the URL query parameter
if (isset($_GET['id'])) {
    $studentId = intval($_GET['id']);
    $query = mysqli_query($conn, "SELECT * FROM tblstudents WHERE Id = $studentId");
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
    } else {
        echo "User not found.";
        exit;
    }
} else {
    echo "Student ID not provided.";
    exit;
}

// Fetch class and class arm details by joining the necessary tables
$classQuery = mysqli_query($conn, "SELECT c.className, ca.classArmName 
                                   FROM tblstudent_class sc 
                                   JOIN tblclass c ON sc.class_id = c.Id 
                                   JOIN tblclassarms ca ON sc.class_arm_id = ca.Id 
                                   WHERE sc.student_id = $studentId");
$classes = [];
if (mysqli_num_rows($classQuery) > 0) {
    while ($classRow = mysqli_fetch_assoc($classQuery)) {
        $classes[] = $classRow;
    }
}

// Define the upload directory
$uploadDirectory = '../uploads/';

// Download images
$livePhotoPath = $uploadDirectory . 'live_photo_' . $studentId . '.jpg';
$idFrontPath = $uploadDirectory . 'id_front_' . $studentId . '.jpg';
$idBackPath = $uploadDirectory . 'id_back_' . $studentId . '.jpg';

if (!downloadGoogleDriveFile(extractGoogleDriveFileId($user['livePhoto']), $livePhotoPath)) {
    $livePhotoPath = 'default_live_photo.jpg'; // Default image if download fails
}

if (!downloadGoogleDriveFile(extractGoogleDriveFileId($user['idFront']), $idFrontPath)) {
    $idFrontPath = 'default_id_front.jpg'; // Default image if download fails
}

if (!downloadGoogleDriveFile(extractGoogleDriveFileId($user['idBack']), $idBackPath)) {
    $idBackPath = 'default_id_back.jpg'; // Default image if download fails
}

// Fetch user details based on the student ID from the URL query parameter
if (isset($_GET['id'])) {
    $studentId = intval($_GET['id']);
    $query = mysqli_query($conn, "SELECT * FROM tblstudents WHERE Id = $studentId");
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
    } else {
        echo "User not found.";
        exit;
    }
} else {
    echo "Student ID not provided.";
    exit;
}

// Fetch class and class arm details by joining the necessary tables
$classQuery = mysqli_query($conn, "SELECT c.className, ca.classArmName 
                                   FROM tblstudent_class sc 
                                   JOIN tblclass c ON sc.class_id = c.Id 
                                   JOIN tblclassarms ca ON sc.class_arm_id = ca.Id 
                                   WHERE sc.student_id = $studentId");
$classes = [];
if (mysqli_num_rows($classQuery) > 0) {
    while ($classRow = mysqli_fetch_assoc($classQuery)) {
        $classes[] = $classRow;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="img/logo/attnlg.jpg" rel="icon">
    <title>Student Details</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <style>
        .card-header {
            background-color: #4e73df;
            color: #fff;
            font-weight: bold;
        }
        .image-wrapper {
            position: relative;
            display: inline-block;
        }
        .edit-icon {
            position: absolute;
            top: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.8);
            color: #fff;
            padding: 5px;
            border-radius: 50%;
        }
        .image-container {
            margin-bottom: 15px;
        }
        .img-circle {
            border-radius: 50%;
        }
        .d-none {
            display: none;
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
                        <h1 class="h3 mb-0 text-gray-800">Student Details</h1>
                    </div>

                    <!-- Personal Details and Status Section -->
                    <div class="row">
                        <!-- Personal Details Section -->
                        <div class="col-lg-6">
                            <div class="card mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold">Personal Details</h6>
                                </div>
                                <div class="card-body text-center">
                                    <div class="image-container">
                                        <img src="<?php echo $livePhotoPath; ?>" class="img-thumbnail img-circle" style="height:150px;width:150px;">
                                        <img src="http://att.neeviitgn.com/uploads/<?php echo $user['livePhoto']; ?>" class="img-thumbnail img-circle" style="height:150px;width:150px;">
                                        <!-- Edit icon for Live Photo -->
                                        <a href="#" data-toggle="modal" data-target="#editLivePhotoModal" class="edit-icon"><i class="fas fa-edit"></i></a>
                                    </div>
                                    <p><strong>ID Number:</strong> <?php echo $user['aadharNumber']; ?></p>
                                    <p><strong>Surname:</strong> <?php echo $user['surname']; ?></p>
                                    <p><strong>Name:</strong> <?php echo $user['name']; ?></p>
                                    <p><strong>Second Name:</strong> <?php echo $user['secondName']; ?></p>
                                    <p><strong>Mobile Number:</strong> <?php echo $user['mobile']; ?></p>
                                    <p><strong>Alternate Mobile Number:</strong> <?php echo $user['alternateMobile']; ?></p>
                                    <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                                    <p><strong>Age:</strong> <?php echo $user['age']; ?></p>
                                    <p><strong>Date of Birth:</strong> <?php echo $user['dob']; ?></p>
                                    <p><strong>Gender:</strong> <?php echo $user['gender']; ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Status Section -->
                        <div class="col-lg-6">
                            <div class="card mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold">Status</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Education:</strong> <?php echo $user['education']; ?></p>
                                    <p><strong>Current Status:</strong> <?php echo $user['currentStatus']; ?></p>
                                    <p><strong>Family Working Status:</strong> <?php echo $user['familyWorkingStatus']; ?></p>
                                    <p><strong>Work Experience:</strong> <?php echo $user['workExperience']; ?></p>
                                </div>
                            </div>

                            <!-- Address Section -->
                            <div class="card mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold">Address</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Village:</strong> <?php echo $user['village']; ?></p>
                                    <p><strong>Current Address:</strong> <?php echo $user['currentAddress']; ?></p>
                                    <p><strong>Permanent Address:</strong> <?php echo $user['permanentAddress']; ?></p>
                                </div>
                            </div>

                            <!-- Fees Section -->
                            <div class="card mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold">Fees</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Deposit Status:</strong> <?php echo $user['deposited']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enrollment Section -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold">Enrollment</h6>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($classes)) { ?>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>S.NO</th>
                                                    <th>Class</th>
                                                    <th>Batch</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($classes as $index => $class) { ?>
                                                    <tr>
                                                        <td><?php echo $index + 1; ?></td>
                                                        <td><?php echo $class['className']; ?></td>
                                                        <td><?php echo $class['classArmName']; ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    <?php } else { ?>
                                        <p>No enrollment details found.</p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aadhar Card Details Section -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold">ID Card Details</h6>
                                </div>
                                <div class="card-body text-center">
                                    <p><strong>ID Number:</strong> <?php echo $user['aadharNumber']; ?></p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <img src="<?php echo $idFrontPath; ?>" class="img-thumbnail" style="height:200px;width:300px;">
                                            <img src="http://att.neeviitgn.com/uploads/<?php echo $user['idFront']; ?>" class="img-thumbnail" style="height:200px;width:300px;">
                                            <p><strong>Front ID </strong></p>
                                            <!-- Edit icon for ID Front -->
                                            <a href="#" data-toggle="modal" data-target="#editIdFrontModal" class="edit-icon"><i class="fas fa-edit"></i></a>
                                        </div>
                                        <div class="col-md-6">
                                             <img src="<?php echo $idBackPath; ?>" class="img-thumbnail" style="height:200px;width:300px;">
                                            <img src="http://att.neeviitgn.com/uploads/<?php echo $user['idBack']; ?>" class="img-thumbnail" style="height:200px;width:300px;">
                                            <p><strong>Back ID </strong></p>
                                            <!-- Edit icon for ID Back -->
                                            <a href="#" data-toggle="modal" data-target="#editIdBackModal" class="edit-icon"><i class="fas fa-edit"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer -->
            <?php include "Includes/footer.php"; ?>
            <!-- Footer -->
        </div>
    </div>

    <!-- Modal for Editing Live Photo -->
    <!-- Modal for editing Live Photo -->
    <div class="modal fade" id="editLivePhotoModal" tabindex="-1" role="dialog" aria-labelledby="editLivePhotoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="update_image.php" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editLivePhotoModalLabel">Edit Live Photo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="studentId" value="<?php echo $studentId; ?>">
                        <input type="hidden" name="imageType" value="livePhoto">
                        <input type="file" name="livePhoto" class="form-control-file" accept="image/*">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal for editing ID Front Photo -->
    <div class="modal fade" id="editIdFrontModal" tabindex="-1" role="dialog" aria-labelledby="editIdFrontModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="update_image.php" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editIdFrontModalLabel">Edit ID Front Photo</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="studentId" value="<?php echo $studentId; ?>">
                        <input type="hidden" name="imageType" value="idFront">
                        <input type="file" name="idFront" class="form-control-file" accept="image/*">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Modal for editing ID Back Photo -->
<div class="modal fade" id="editIdBackModal" tabindex="-1" role="dialog" aria-labelledby="editIdBackModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="update_image.php" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editIdBackModalLabel">Edit ID Back Photo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="studentId" value="<?php echo $studentId; ?>">
                    <input type="hidden" name="imageType" value="idBack">
                    <input type="file" name="idBack" class="form-control-file" accept="image/*">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
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
    <script>
        function toggleSection(sectionId) {
            var section = document.getElementById(sectionId);
            if (section.classList.contains('d-none')) {
                section.classList.remove('d-none');
            } else {
                section.classList.add('d-none');
            }
        }
    </script>
</body>
</html>
