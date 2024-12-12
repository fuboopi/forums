<?php
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $input = file_get_contents('php://input');
    
    
    $data = json_decode($input, true); 
    
    if ($data) {
        $post_id = isset($data['post_id']) ? $data['post_id'] : null;

        
        $type = isset($data['type']) ? ($data['type'] === "upvote" ? 1 : ($data['type'] === "downvote" ? -1 : 0)) : 0;

        
        $checkQuery = "SELECT * FROM forum_posts_votes WHERE uid = ? AND post_id = ?";
        $checkStmt = $link->prepare($checkQuery);
        $checkStmt->bind_param("ii", $_SESSION['uid'], $post_id);
        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            
            $updateQuery = "UPDATE forum_posts_votes SET type = ? WHERE uid = ? AND post_id = ?";
            $updateStmt = $link->prepare($updateQuery);
            $updateStmt->bind_param("iii", $type, $_SESSION['uid'], $post_id);

            if ($updateStmt->execute()) {
                echo json_encode([
                    'message' => "Vote updated for post_id: $post_id, type: $type"
                ]);
            } else {
                echo json_encode([
                    'message' => "Error updating vote",
                    'error' => $updateStmt->error 
                ]);
            }
        } else {
            
            $insertQuery = "INSERT INTO forum_posts_votes (uid, post_id, type) VALUES (?, ?, ?)";
            $insertStmt = $link->prepare($insertQuery);
            $insertStmt->bind_param("iii", $_SESSION['uid'], $post_id, $type);

            if ($insertStmt->execute()) {
                echo json_encode([
                    'message' => "Received post_id: $post_id, type: $type"
                ]);
            } else {
                echo json_encode([
                    'message' => "Error inserting vote",
                    'error' => $insertStmt->error 
                ]);
            }
        }

    } else {
        echo json_encode(['error' => 'Invalid JSON']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
