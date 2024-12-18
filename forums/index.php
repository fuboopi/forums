<?php
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');

if (!isset($_GET['forum_id']) || !is_numeric($_GET['forum_id'])) {
    echo "Invalid forum ID.";
    exit;
}

$forum_id = (int)$_GET['forum_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['uid'])) {
        echo "You need to log in to post a reply.";
        exit;
    }
    $reply_content = mysqli_real_escape_string($link, $_POST['content']);
    $reply_content = str_replace('\r\n', '\n', $reply_content);
    $created_by = $_SESSION['uid'];

    include($_SERVER['DOCUMENT_ROOT'] . '/script/forums/post_upload_file.php');

    if (mysqli_stmt_execute($stmt)) {
        header("Location: /forums/index.php?forum_id=" . $forum_id);
        exit;
    } else {
        echo "Error: " . mysqli_error($link);
    }

    mysqli_stmt_close($stmt);
}

$query = "SELECT forums.*, users.name AS creator_name, users.uid AS creator_id FROM forums 
          LEFT JOIN users ON forums.created_by = users.uid 
          WHERE forum_id = ?";
$stmt = mysqli_prepare($link, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $forum_id);

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $forum = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
} else {
    echo "Error: " . mysqli_error($link);
}

if (!$forum) {
    echo "Forum not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum: <?php echo htmlspecialchars($forum['name']); ?> | <?php echo $site_name?></title>
    <link rel="stylesheet" href="<?php echo $stylesheet; ?>">
    <link rel="stylesheet" href="/style/forums.css">
    <script src='/script/forums/vote.js'></script>
    <script src='/script/forums/post_options.js'></script>
</head>
<body>

    <div class="container" id='forum_frame'>
        <h1 class='forum_title'><?php echo htmlspecialchars($forum['name']); ?></h1>
        <p><small>Created by <a href="/account/profile?uid=<?php echo $forum['creator_id']; ?>"><?php echo htmlspecialchars($forum['creator_name']); ?></a></small></p>
        <p><?php echo htmlspecialchars($forum['description']); ?></p>

        <div class="reply-container">
            <h2>Replies:</h2>
            <?php
            $replies_query = "SELECT forum_posts.*, users.name AS username, users.uid AS user_id FROM forum_posts 
            LEFT JOIN users ON forum_posts.created_by = users.uid 
            WHERE forum_posts.forum_id = ? 
            ORDER BY forum_posts.created_at ASC";
            $stmt = mysqli_prepare($link, $replies_query);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $forum_id);

                mysqli_stmt_execute($stmt);
                $replies_result = mysqli_stmt_get_result($stmt);

                mysqli_stmt_close($stmt);
            } else {
                echo "Error: " . mysqli_error($link);
            }

            if (mysqli_num_rows($replies_result) > 0) {
                while ($reply = mysqli_fetch_assoc($replies_result)) {
                    $query = "SELECT type FROM forum_posts_votes WHERE post_id = ? AND uid = ?";
                    $stmt = $link->prepare($query);
                    $stmt->bind_param("ii", $reply['post_id'], $_SESSION['uid']);
                    $stmt->execute();
                    $result = $stmt->get_result();
        
                    $vote_type = "0";
                    if ($result->num_rows > 0) {
                        $vote_type = $result->fetch_assoc()['type'];
                    }

                    $query = "SELECT SUM(type) AS net_votes FROM forum_posts_votes WHERE post_id = ?";
                    $stmt = $link->prepare($query);
                    $stmt->bind_param("i", $reply['post_id']);
                    $stmt->execute();
                    $netVotes = $stmt->get_result()->fetch_assoc()['net_votes'];
                    if ($netVotes == NULL) {
                        $netVotes = "0";
                    }
                    $pfp_query = "SELECT picture_dir FROM users WHERE uid = ?";
                    $stmt = mysqli_prepare($link, $pfp_query);

                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "i", $reply['user_id']);
                        mysqli_stmt_execute($stmt);
                        $pfp_result = mysqli_stmt_get_result($stmt);
                        if ($pfp_result && mysqli_num_rows($pfp_result) > 0) {
                            $pfp_row = mysqli_fetch_assoc($pfp_result);
                            $picture_dir = $pfp_row['picture_dir'] ?? '/images/default-avatar.png';
                        } else {
                            $picture_dir = '/images/default-avatar.png';
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        echo "Error: " . mysqli_error($link);
                    }
                    ?>
                    <div class="reply-item">
                        <div class='left'>
                            <a href="/account/profile?uid=<?php echo $reply['user_id']; ?>"  class='user'>
                                <img src='<?php echo $picture_dir?>' style='border-radius: 100%; max-width: 75%' loading="lazy">
                                <p><?php echo htmlspecialchars($reply['username']); ?></p>
                            </a>
                            <br>
                            <p><small><?php echo date("m/d/y h:iA", strtotime($reply['created_at'])); ?></small></p>
                            <div class='bottom'>
                                <?php if ($reply['user_id'] == $_SESSION['uid']): ?>
                                <a class='delete-post' id='delete-<?php echo $reply['post_id'];?>'><i class="fa-solid fa-trash"></i></a>
                                <a class='edit-post' id='edit-<?php echo $reply['post_id'];?>'><i class="fa-solid fa-pen-to-square"></i></a>
                                <?php endif;?>
                            </div>
                        </div>
                        <div class='main'>
                            <?php
                            $formatted = nl2br(stripcslashes($reply['content']));
                            ?>
                            <p class='reply-content'><?php echo $formatted; ?></p>
                            <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/replies_file.php'); ?>
                        </div>
                        <div class='right'>
                            <div class='vote-container'>
                                <h2 class='vote-count' id='vote-count<?php echo $reply['post_id'];?>'><?php echo $netVotes?></h2>
                                <div class='btns'>
                                <a class='upvote <?php echo ($vote_type == 1) ? 'selected' : ''; ?>' id='<?php echo $reply['post_id']; ?>'>
                                    <i class="fa-regular fa-square-caret-up"></i>
                                </a>
                                <a class='downvote <?php echo ($vote_type == -1) ? 'selected' : ''; ?>' id='<?php echo $reply['post_id']; ?>'>
                                    <i class="fa-regular fa-square-caret-down"></i>
                                </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <p>No replies yet.</p>
                <?php
            }
            ?>

            <h2>Post a Reply</h2>
            <?php if (isset($_SESSION['uid'])): ?>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="forum_id" value="<?php echo $forum_id; ?>">
                    <textarea name="content" required></textarea><br>
                    <label for="file">Attach a file (optional):</label>
                    <input type="file" name="file" id="file"><br>
                    <button type="submit">Submit Reply</button>
                </form>
            <?php else: ?>
                <p><a href="/account/login">Log in</a> to post a reply.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>

<?php
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    $file_query = "SELECT file, file_name FROM forum_posts WHERE post_id = ?";
    $stmt = mysqli_prepare($link, $file_query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $post_id);
        mysqli_stmt_execute($stmt);
        $file_result = mysqli_stmt_get_result($stmt);
        $file_row = mysqli_fetch_assoc($file_result);
        if ($file_row && $file_row['file']) {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file_row['file_name'] . '"');
            echo $file_row['file'];
            exit;
        } else {
            echo "File not found.";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($link);
    }
}
?>
<?php
include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
?>