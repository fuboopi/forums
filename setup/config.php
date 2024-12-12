<?php
session_set_cookie_params(2592000);
session_start();

// Setup MySQL Database in ./db_config.php
$dbConfig = require 'db_config.php';
define("DB_SERVER", $dbConfig['server']);
define("DB_USERNAME", $dbConfig['username']);
define("DB_PASSWORD", $dbConfig['password']);
define("DB_NAME", $dbConfig['name']);

// Variables
$site = "https://forums.fuboopi.com";
$site_name = "fuboopi";

define("ROOTPATH", __DIR__);
$cdnDIR = '/CDN/forums';
$cdn = "https://cdn.fuboopi.com/forums";

// variables that u probably dont need to change lol
$token_name = 'remember_me';

// ------------------------------------------------------------------

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

$token_name = mysqli_real_escape_string($link, $token_name);
$query = "SELECT * FROM `users` WHERE `remember_token` = '$_COOKIE[$token_name]' LIMIT 1";
$result = mysqli_query($link, $query);

if ($result && mysqli_num_rows($result) > 0 ) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION['uid'] = $row['uid'];
    $_SESSION['name'] = $row['name'];

} else {
}

if ($link == false) {
    die("Could not connect to database: ". mysqli_connect_error());
} else {
    if (isset($_SESSION['uid'])) {
        $uid = $_SESSION['uid'];

        // Dark Mode
        $query = "SELECT dark_mode, error_reporting FROM users WHERE uid = ?";
        $stmt = $link->prepare($query);
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if ($user['dark_mode'] == 1) {
                $stylesheet = "/style/style_dark.css";
            } else {
                $stylesheet = "/style/style_light.css";
            }
        } else {
            $stylesheet = "/style/style_light.css";
        }
        $stmt->close();

        if ($result->num_rows >0 ) {
            if ($user['error_reporting'] == 1){
                ini_set('display_errors', 1);
                ini_set('display_startup_errors', 1);
                error_reporting(E_ALL);
            }

        }
    } else {
        // If not logged in
        $stylesheet = "/style/style_light.css";
    }


}
?>


