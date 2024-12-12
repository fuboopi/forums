<?php
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');


$userId = $_SESSION['uid'];
$recipientId = $_POST['recipientId'];


$sql = "SELECT conversation_id FROM conversations 
        WHERE (user_1_id = ? AND user_2_id = ?) OR (user_1_id = ? AND user_2_id = ?)";
$stmt = $link->prepare($sql);
$stmt->bind_param("iiii", $userId, $recipientId, $recipientId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$conversation = $result->fetch_assoc();

if (!$conversation) {
    
    $sql = "INSERT INTO conversations (user_1_id, user_2_id) VALUES (?, ?)";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("ii", $userId, $recipientId);
    $stmt->execute();
    $conversationId = $stmt->insert_id; 
} else {
    
    $conversationId = $conversation['conversation_id'];
}


$sql = "SELECT name, picture FROM users WHERE uid = ?";
$stmt = $link->prepare($sql);
$stmt->bind_param("i", $recipientId);
$stmt->execute();
$result = $stmt->get_result();
$recipient = $result->fetch_assoc();


echo json_encode([
    'conversationId' => $conversationId,
    'recipientId' => $recipientId,
    'recipientName' => $recipient['name'],
    'recipientPicture' => base64_encode($recipient['picture']) 
]);
?>
