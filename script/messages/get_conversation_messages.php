<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');

$conversationId = $_GET['conversationId'] ?? null;

if ($conversationId) {
    
    $sql = "SELECT m.content, m.timestamp, u.name AS sender_name 
            FROM messages m
            JOIN users u ON m.sender_id = u.uid
            WHERE m.conversation_id = ?
            ORDER BY m.timestamp ASC"; 

    $stmt = $link->prepare($sql);
    $stmt->bind_param("i", $conversationId);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'content' => $row['content'],
            'timestamp' => $row['timestamp'],
            'senderName' => $row['sender_name']
        ];
    }

    echo json_encode($messages);
} else {
    echo json_encode([]);
}
?>
