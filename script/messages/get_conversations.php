<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');
header('Content-Type: application/json');  


if (!isset($_SESSION['uid'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}


$sql = "SELECT c.conversation_id, u1.uid AS user_1_id, u2.uid AS user_2_id, 
               u1.name AS user_1_name, u2.name AS user_2_name, 
               u1.picture AS user_1_picture, u2.picture AS user_2_picture 
        FROM conversations c
        JOIN users u1 ON c.user_1_id = u1.uid
        JOIN users u2 ON c.user_2_id = u2.uid
        WHERE c.user_1_id = ? OR c.user_2_id = ?";

$stmt = $link->prepare($sql);
$stmt->bind_param("ii", $_SESSION['uid'], $_SESSION['uid']);
$stmt->execute();
$result = $stmt->get_result();

$conversations = [];
while ($row = $result->fetch_assoc()) {
    
    $row['user_1_picture'] = $row['user_1_picture'] ? base64_encode($row['user_1_picture']) : '';
    $row['user_2_picture'] = $row['user_2_picture'] ? base64_encode($row['user_2_picture']) : '';
    
    
    if ($_SESSION['uid'] == $row['user_1_id']) {
        $row['recipientName'] = $row['user_2_name'];
        $row['recipientPicture'] = $row['user_2_picture'];
    } else {
        $row['recipientName'] = $row['user_1_name'];
        $row['recipientPicture'] = $row['user_1_picture'];
    }
    $conversations[] = $row;
}


echo json_encode(['conversations' => $conversations]);
?>
