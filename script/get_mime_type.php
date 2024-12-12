<?php
function get_image_mime_type(string $image_path): ?string {
    
    if (!filter_var($image_path, FILTER_VALIDATE_URL)) {
        return null; 
    }

    
    $headers = get_headers($image_path, 1);

    if ($headers === false) {
        return null; 
    }

    
    if (strpos($headers[0], '404') !== false) {
        return null; 
    }

    
    if (isset($headers['Content-Type'])) {
        return $headers['Content-Type']; 
    }

    
    $image_info = getimagesize($image_path); 

    if ($image_info === false) {
        return null; 
    }

    return $image_info['mime']; 
}
?>
