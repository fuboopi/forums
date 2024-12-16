<?php
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');

$data = json_decode(file_get_contents("php://input"), true);

$query = 'SELECT * FROM forum_posts WHERE post_id = ?';
$stmt = $link->prepare($query);
$stmt->bind_param('i', $data['post_id']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($_SESSION['uid'] == $row['created_by']){
    $query = 'DELETE FROM forum_posts WHERE post_id = ?';
    $stmt = $link->prepare($query);
    $stmt->bind_param('i', $data['post_id']);
    $stmt->execute();
};
?>
