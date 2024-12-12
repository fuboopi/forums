<?php
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');

if (!isset($_SESSION['uid'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['uid'];
$otherUserId = $_GET['otherUserId'];

$sql = "SELECT * FROM messages 
        WHERE (sender_id = ? AND receiver_id = ?) 
        OR (sender_id = ? AND receiver_id = ?) 
        ORDER BY timestamp ASC";

$stmt = $link->prepare($sql);
$stmt->bind_param("iiii", $userId, $otherUserId, $otherUserId, $userId);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
?>
