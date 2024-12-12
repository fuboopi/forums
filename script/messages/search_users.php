<?php
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');

if (!isset($_GET['query']) || empty($_GET['query'])) {
    echo json_encode([]);
    exit;
}

$searchQuery = "%" . $_GET['query'] . "%";

$sql = "SELECT uid, name FROM users WHERE name LIKE ? AND uid != ? LIMIT 10";
$stmt = $link->prepare($sql);
$stmt->bind_param("si", $searchQuery, $_SESSION['uid']);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

if (empty($users)) {
    echo json_encode([]);
    exit;
}

echo json_encode($users);
