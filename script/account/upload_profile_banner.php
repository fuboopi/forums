<?php
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');

if (isset($_FILES['croppedImage']) && $_FILES['croppedImage']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['croppedImage']['tmp_name'];
    $fileName = $_FILES['croppedImage']['name'];
    $fileSize = $_FILES['croppedImage']['size'];
    $fileType = $_FILES['croppedImage']['type'];

    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . $cdnDIR.'/profile_banners/';  

    $newFileName = uniqid('profile_', true) . '.png';
    $destPath = $uploadDir . $newFileName;
    $cdnLink = str_replace($_SERVER['DOCUMENT_ROOT']."/CDN/forums", "$cdn", $destPath);
    if (move_uploaded_file($fileTmpPath, $destPath)) {
        $query = "UPDATE users SET banner_dir = ? WHERE uid = ?";
        $stmt = $link->prepare($query);
        $stmt->bind_param("si", $cdnLink, $uid);

        if ($stmt->execute()) {
        } else {
            $error_message = "There was an error updating your profile. Please try again.";
        };
        echo "Successfully Updated Profile Banner";
    } else {
        echo "There was an error uploading the file.";
    }
} else {
    echo "No file uploaded or an error occurred during the upload.";
}

?>
