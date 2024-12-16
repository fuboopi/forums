<?php
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');

$profile_uid = isset($_GET['uid']) && is_numeric($_GET['uid']) ? $_GET['uid'] : $_SESSION['uid'];

$query = "
    SELECT u.*, 
           (SELECT COUNT(*) FROM forum_posts WHERE created_by = u.uid) AS post_count,
           (SELECT COUNT(*) FROM forums WHERE created_by = u.uid) AS forum_count
    FROM users u
    WHERE u.uid = ?
    LIMIT 1
";
$stmt = $link->prepare($query);
$stmt->bind_param("i", $profile_uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "<p>User not found!</p>";
    exit();
}
$profile_picture = $user['picture_dir'] ?? ($user['picture'] ? 'data:image/jpeg;base64,' . base64_encode($user['picture']) : '/images/default-avatar.png');
$banner_picture = $user['banner_dir'] ?? '/images/default-avatar.png';
$join_date = date('F d, Y', strtotime($user['joined_on']));

$query_posts = "SELECT * FROM forum_posts WHERE created_by = ? ORDER BY created_at DESC LIMIT 5";
$stmt_posts = $link->prepare($query_posts);
$stmt_posts->bind_param("i", $profile_uid);
$stmt_posts->execute();
$recent_posts = $stmt_posts->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($user['name']); ?> | Profile</title>
    <link rel="stylesheet" href="<?php echo $stylesheet ?>">
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
                        <p><strong>Joined:</strong> <?php echo $join_date; ?></p>
                        <p><strong>Forums:</strong> <?php echo $user['forum_count'];?> | <strong>Posts:</strong> <?php echo $user['post_count']; ?></p>
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
