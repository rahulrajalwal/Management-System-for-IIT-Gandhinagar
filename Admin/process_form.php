<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if file fields are set and not empty
    if (isset($_FILES['livePhoto']) && !empty($_FILES['livePhoto']['name'])) {
        $livePhoto = $_FILES['livePhoto'];
        // Process the uploaded file (move to a directory, store in database, etc.)
        $uploadPath = 'uploads/live_photos/';
        $uploadedFile = $uploadPath . basename($livePhoto['name']);
        if (move_uploaded_file($livePhoto['tmp_name'], $uploadedFile)) {
            echo "Live Photo uploaded successfully.";
            // Store $uploadedFile path in database or perform further actions
        } else {
            echo "Failed to upload Live Photo.";
        }
    }

    // Repeat similar process for idFront and idBack files
    if (isset($_FILES['idFront']) && !empty($_FILES['idFront']['name'])) {
        $idFront = $_FILES['idFront'];
        $uploadPath = 'uploads/id_cards/';
        $uploadedFile = $uploadPath . basename($idFront['name']);
        if (move_uploaded_file($idFront['tmp_name'], $uploadedFile)) {
            echo "ID Front uploaded successfully.";
            // Store $uploadedFile path in database or perform further actions
        } else {
            echo "Failed to upload ID Front.";
        }
    }

    if (isset($_FILES['idBack']) && !empty($_FILES['idBack']['name'])) {
        $idBack = $_FILES['idBack'];
        $uploadPath = 'uploads/id_cards/';
        $uploadedFile = $uploadPath . basename($idBack['name']);
        if (move_uploaded_file($idBack['tmp_name'], $uploadedFile)) {
            echo "ID Back uploaded successfully.";
            // Store $uploadedFile path in database or perform further actions
        } else {
            echo "Failed to upload ID Back.";
        }
    }
}
?>
