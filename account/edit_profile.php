<?php
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');

$uid = $_SESSION['uid'];

$query = "SELECT * FROM users WHERE uid = ? LIMIT 1";
$stmt = $link->prepare($query);
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "<p>User not found!</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $bio = trim($_POST['bio']);
    if ($email == ""){
        $email_visible = isset($_POST['email_visible']) ? 0 : 0;
    } else {
        $email_visible = isset($_POST['email_visible']) ? 1 : 0;
    };
    $dark_mode = isset($_POST['dark_mode']) ? 1 : 0;
    $error_reporting = isset($_POST['error_reporting']) ? 1 : 0;

    
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $profile_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
    } else {
        $profile_picture = $user['picture'];
    }

    if (empty($name)) {
        $error_message = "Name is required!";
    } else {
        $query = "UPDATE users SET name = ?, email = ?, bio = ?, email_visible = ?, dark_mode = ?, error_reporting = ? WHERE uid = ?";
        $stmt = $link->prepare($query);
        $stmt->bind_param("sssiiii", $name, $email, $bio, $email_visible, $dark_mode, $error_reporting, $uid);

        if ($stmt->execute()) {
            header("Location: /account/profile.php");
            exit();
        } else {
            $error_message = "There was an error updating your profile. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="<?php echo $stylesheet ?>">
    <link rel="stylesheet" href="/style/profile.css">
    <link  href="/node_modules/cropperjs/dist/cropper.css" rel="stylesheet">
    <script src="/node_modules/cropperjs/dist/cropper.js"></script>
    <script src="/script/account/upload_profile_picture.js"></script>
    <script src="/script/account/verify_email_button.js"></script>
    
</head>
<body>

<div class="profile-container">
    <h1>Edit Profile</h1>

    <?php if (isset($error_message)) : ?>
        <p style="color:red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
        <h2>Profile Settings</h2>
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Optional..." value="<?php echo htmlspecialchars($user['email']); ?>">
            <button type='button' id='email-verify'>Verify...</button>
        </div>

        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea name="bio" id="bio" placeholder="Optional..." maxlength="250"><?php echo htmlspecialchars($user['bio']); ?></textarea>
        </div>


        <div class="form-group">
            <label for="profile_picture">Profile Picture</label>
            <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
            <div id="crop-container" style="display: none;">
                <img id="imagePreview" style="max-width: 100%; margin-top: 20px;">
            </div>
            <button type="button" id="cropButton" style="display: none; margin-top: 20px;">Upload...</button>
        </div>

        <div class="form-group">
            <label for="profile_banner">Profile Banner</label>
            <input type="file" name="profile_banner" id="profile_banner" accept="image/*">
            <div id="crop-container-banner" style="display: none;">
                <img id="imagePreview-banner" style="max-width: 100%; margin-top: 20px;">
            </div>
            <button type="button" id="cropButton-banner" style="display: none; margin-top: 20px;">Upload...</button>
        </div>
        <br>
        <h2> Account Options</h2>
        <!--<div class="form-group-checkbox">
            <label for="email_visible" class="inline-label" title='Only Visible to Verified Users'>Show Email on Profile</label>
            <input type="checkbox" name="email_visible" id="email_visible" value="1" <?php echo $user['email_visible'] == 1 ? 'checked' : ''; ?>>
        </div>!-->

        <div class="form-group-checkbox">
            <label for="dark_mode" class="inline-label">Toggle Dark Mode</label>
            <input type="checkbox" name="dark_mode" id="dark_mode" value="1" <?php echo $user['dark_mode'] == 1 ? 'checked' : ''; ?>>
        </div>

        <div class="form-group-checkbox">
            <label for="error_reporting" class="inline-label">Toggle Error Reporting</label>
            <input type="checkbox" name="error_reporting" id="error_reporting" value="1" <?php echo $user['error_reporting'] == 1 ? 'checked' : ''; ?>>
        </div>
        <br>
        <a href="/account/logout?force=1" style='font-size:8pt;'>Sign Out of All Sessions</a>
        <br>
        <br>
        <button type="submit">Update Profile</button>
    </form>

    <a href="/account/profile" class="btn">Back to Profile</a>
</div>

</body>
</html>
<?php
include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
?>