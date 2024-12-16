<?php
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');

if (!isset($_GET['post_id']) || !is_numeric($_GET['post_id'])) {
    echo "Invalid post ID.";
    exit;
}

$query = "SELECT forum_posts.*, users.name, users.picture_dir
    FROM forum_posts
    JOIN users ON forum_posts.created_by = users.uid
    WHERE forum_posts.post_id = ?
";
$stmt = $link->prepare($query);
$stmt->bind_param('i', $_GET['post_id']);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if ($_SESSION['uid'] != $post['created_by']) {
    echo "You are not authorized to edit this post.";
    exit;
};

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $forum_id = $post['forum_id'];
    $reply_content = mysqli_real_escape_string($link, $_POST['content']);
    $reply_content = str_replace('\r\n', '\n', $reply_content);
    $created_by = $_SESSION['uid'];
    $insert_reply_query = "UPDATE forum_posts SET forum_id = ?, content = ?, created_by = ? WHERE post_id = ?";
    $stmt = mysqli_prepare($link, $insert_reply_query);
    mysqli_stmt_bind_param($stmt, 'isii', $forum_id, $reply_content, $created_by, $_GET['post_id']);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: /forums/index.php?forum_id=" . $forum_id);
        exit;
    } else {
        echo "Error: " . mysqli_error($link);
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editing Post | <?php echo $site_name?></title>
    <link rel="stylesheet" href="<?php echo $stylesheet; ?>">
    <link rel="stylesheet" href="/style/forums.css">
    <script src='/script/forums/edit_post.js'></script>
</head>
<body>
    <div class='container'>
        <h1>Editing Reply</h1>
        <br>
        <h3>Preview:</h3>
        <div class='reply-container' style='margin-top: 0px'>
            <div class='reply-item'>
                <div class='left'>
                    <a href="/account/profile?uid=<?php echo $post['created_by']; ?>"  class='user'>
                        <img src='<?php echo $post['picture_dir'];?>' style='border-radius: 100%; max-width: 75%' loading="lazy">
                        <p><?php echo htmlspecialchars($post['name']); ?></p>
                    </a>
                    <br>
                    <p><small><?php echo date("m/d/y h:iA", strtotime($post['created_at'])); ?></small></p>
                </div>
                <div class='main'>
                    <?php
                    $formatted = nl2br(stripcslashes($post['content']));
                    ?>
                    <p class='reply-content' id='preview-content'><?php echo $formatted;?></p>
                    <?php if($post['file_dir']):?>
                    <img src='<?php echo $post['file_dir'];?>' alt='Attached Image' style='max-width: 100%; height: 200px;' loading='lazy'>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="forum_id" value="<?php echo $post['forum_id']; ?>">
            <textarea name="content" id='edit-input' required><?php echo str_replace('\n', PHP_EOL, $post['content']);?></textarea><br>
            <button type="submit">Update Reply</button>
        </form>
    </div>
</body>
<?php
include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
?>