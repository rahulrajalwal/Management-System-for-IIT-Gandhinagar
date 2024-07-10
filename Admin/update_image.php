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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = intval($_POST['studentId']);
    $imageType = $_POST['imageType'];
    $uploadMethod = $_POST['uploadMethod'];
    $uploadDirectory = '../uploads/';
    $destinationPath = $uploadDirectory . $imageType . '_' . $studentId . '.jpg';

    if ($uploadMethod === 'file' && isset($_FILES[$imageType . 'File'])) {
        // Handle file upload from PC
        $fileTmpPath = $_FILES[$imageType . 'File']['tmp_name'];
        move_uploaded_file($fileTmpPath, $destinationPath);
    } elseif ($uploadMethod === 'url' && !empty($_POST[$imageType . 'Url'])) {
        // Handle Google Drive URL
        $fileId = extractGoogleDriveFileId($_POST[$imageType . 'Url']);
        if (!downloadGoogleDriveFile($fileId, $destinationPath)) {
            echo "Failed to download image from Google Drive.";
            exit;
        }
    } else {
        echo "Invalid upload method.";
        exit;
    }

    // Update the database record
    $updateQuery = "UPDATE tblstudents SET $imageType = '$destinationPath' WHERE Id = $studentId";
    if (mysqli_query($conn, $updateQuery)) {
        echo "Image updated successfully.";
    } else {
        echo "Failed to update image in the database.";
    }
}
?>
