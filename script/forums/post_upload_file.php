<?php
$file_data = null;
$file_dir_db = null;
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . $cdnDIR.'/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_name = $_FILES['file']['name'];
    $file_size = $_FILES['file']['size'];
    $file_type = $_FILES['file']['type'];
    $file_dir = $upload_dir . time() . basename($file_name);

    
    if ($file_size > 10485760) {
        echo "File size exceeds the 10MB limit.";
        exit;
    }

    $file_data = file_get_contents($file_tmp);

    if (move_uploaded_file($file_tmp, $file_dir)) {
        $file_cdn_link = $cdn.'/uploads/' . time() . basename($file_name);
    } else {
        echo "Failed to save file to directory.";
        exit;
    }
}

$insert_reply_query = "INSERT INTO forum_posts (forum_id, content, created_by, file_dir) VALUES (?, ?, ?, ?)";

$stmt = mysqli_prepare($link, $insert_reply_query);

if ($file_data) {
    mysqli_stmt_bind_param($stmt, 'isis', $forum_id, $reply_content, $created_by, $file_cdn_link);
} else {
    $null_value = NULL;
    mysqli_stmt_bind_param($stmt, 'isis', $forum_id, $reply_content, $created_by, $null_value);
}

?>