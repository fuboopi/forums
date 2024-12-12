<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/script/get_mime_type.php');

if ($reply['file_dir']) {
    $file_path = $reply['file_dir'];
    
    $mime_type = get_image_mime_type($file_path);
    
    if ($mime_type && strpos($mime_type, 'image') !== false) { 
        echo "<p class='reply-content'><img src='" . htmlspecialchars($reply['file_dir']) . "' alt='Attached Image' style='max-width: 100%; height: 200px;' loading='lazy'></p>";
    } elseif ($mime_type && strpos($mime_type, 'video') !== false) { 

    } else {
        echo "<p><a href='" . htmlspecialchars($reply['file_dir']) . "' target='_blank'>Download File...</a></p>";
    }
    
} elseif ($reply['file']) {
    
    $mime_type = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $reply['file']);
    
    if (strpos($mime_type, 'image') !== false) {
        $base64_image = base64_encode($reply['file']);
        echo "<p class='reply-content'><img src='data:$mime_type;base64,$base64_image' alt='Reply Image' style='max-width: 100%; height: 200px;' loading='lazy'></p>";
    } else {
        echo "<p><a href='/forums/download.php?post_id=" . $reply['post_id'] . "'>Download File...</a></p>";
    }
}


?>