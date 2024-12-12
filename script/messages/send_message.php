<?php
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');

if (!isset($_SESSION['uid'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$conversationId = isset($_POST['conversationId']) ? intval($_POST['conversationId']) : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

$sql = "SELECT * FROM conversations WHERE conversation_id = ? AND (user_1_id = ? OR user_2_id = ?)";
$stmt = $link->prepare($sql);
$stmt->bind_param("iii", $conversationId, $_SESSION['uid'], $_SESSION['uid']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Conversation not found or user not part of it']);
    exit();
}

$sql = "INSERT INTO messages (conversation_id, sender_id, message, created_at) VALUES (?, ?, ?, NOW())";
$stmt = $link->prepare($sql);
$stmt->bind_param("iis", $conversationId, $_SESSION['uid'], $message);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send message']);
}

if (isset($wsServer)) {
    $messageData = [
        'type' => 'message',
        'senderName' => $_SESSION['user_name'],
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    $wsServer->broadcast(json_encode($messageData)); 
}
?>
