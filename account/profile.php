<?php
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');



if (isset($_GET['uid']) && is_numeric($_GET['uid'])) {
    
    $profile_uid = $_GET['uid'];
} else {
    
    $profile_uid = $_SESSION['uid'];
}


$query = "SELECT * FROM users WHERE uid = ? LIMIT 1";
$stmt = $link->prepare($query);
$stmt->bind_param("i", $profile_uid);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "<p>User not found!</p>";
    exit();
}


if ($user['picture_dir'] != NULL) {
    $profile_picture = $user['picture_dir'];
} elseif ($user['picture']) {
    $profile_picture = 'data:image/jpeg;base64,' . base64_encode($user['picture']);
} else {
    $profile_picture = '/images/default-avatar.png';  
}


if ($user['banner_dir'] != NULL) {
    $banner_picture = $user['banner_dir'];
} else {
    $banner_picture = '/images/default-avatar.png';  
}


$query_posts = "SELECT * FROM forum_posts WHERE created_by = ? ORDER BY created_at DESC LIMIT 5";
$stmt_posts = $link->prepare($query_posts);
$stmt_posts->bind_param("i", $_GET['uid']);
$stmt_posts->execute();
$recent_posts = $stmt_posts->get_result();


$join_date = date('F d, Y', strtotime($user['joined_on']));
    
$query = "SELECT COUNT(*) AS post_count FROM forum_posts WHERE created_by = ?";
$stmt = $link->prepare($query);
$stmt->bind_param("i", $profile_uid);
$stmt->execute();
$post_count = $stmt->get_result()->fetch_assoc();
$post_count = $post_count['post_count'];
    
$query = "SELECT COUNT(*) AS forum_count FROM forums WHERE created_by = ?";
$stmt = $link->prepare($query);
$stmt->bind_param("i", $profile_uid);
$stmt->execute();
$forum_count = $stmt->get_result()->fetch_assoc();
$forum_count = $forum_count['forum_count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($user['name']); ?> | Profile</title>
    <link rel="stylesheet" href="<?php echo $stylesheet ?>">
    <link rel="stylesheet" href="/style/header.css">
    <link rel="stylesheet" href="/style/profile.css">
</head>
<body>

    <div class="profile-container">
        <div class='banner' style='background-image: url("<?php echo $banner_picture;?>")'>
            
        </div>
        <div class="profile-card">
            <div class="profile-header">
                <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" class="profile-picture">
                <h1 class='username'><?php echo htmlspecialchars($user['name']); ?> <?php if ($_SESSION['uid'] == $profile_uid): ?><a href="/account/edit_profile" class="btn">Edit Profile</a><?php endif; ?></h1>
            </div>
            <div class="profile-info">
                    <p><?php echo htmlspecialchars($user['bio']); ?></p>
                    <div class="profile-stats">
                        <p class='email'><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Joined:</strong> <?php echo $join_date; ?></p>
                        <p><strong>Forums:</strong> <?php echo $forum_count;?> | <strong>Posts:</strong> <?php echo $post_count; ?></p>
                        <?php if ($user['email_visible'] == 1): ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
    ?>
</body>
</html>
