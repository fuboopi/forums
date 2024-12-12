<?php

include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');

setcookie('remember_me', '', time() - 3600, '/');

if (isset($_GET['force']) && $_GET['force'] == "1") {
    $token = NULL;
    $update_query = "UPDATE users SET remember_token = ? WHERE uid = ?";
    $stmt = $link->prepare($update_query);
    $stmt->bind_param("si", $token, $_SESSION['uid']);
    $stmt->execute();

    $sessionPath = ini_get('session.save_path');
    $files = scandir($sessionPath);
    print $_SESSION['uid'];
    foreach ($files as $file) {
        if (strpos(file_get_contents($sessionPath . "/" . $file), $_SESSION['name']) !== false) {
            unlink($sessionPath . "/" . $file);
        }
    }
};
session_unset();
session_destroy();

header("Location: /");
exit();
?>
