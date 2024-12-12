<?php
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');


$query = "SELECT forums.*, users.name AS creator_name, users.uid AS creator_id FROM forums 
          LEFT JOIN users ON forums.created_by = users.uid 
          ORDER BY created_at DESC LIMIT 5";
$result = mysqli_query($link, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($link));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forums | <?php echo $site_name?></title>
    
    <link rel="stylesheet" href="<?php echo $stylesheet ?>">
    <link rel="stylesheet" href="/style/header.css">
    <link rel="stylesheet" href="/style/forums.css">
</head>
<body>
    <div class="container">
        <h1>Latest Forums</h1>
        
        <!-- "New Forum" Button -->
        <div class="new-forum-button">
            <a href="/forums/create.php">
                <button>Create New Forum</button>
            </a>
        </div>
        <br>
        <!-- List of the 5 newest forums -->
        <div class="forums-list">
            <?php while ($forum = mysqli_fetch_assoc($result)): ?>
                <div class="forum-item">
                    <!-- Only the title is a clickable link to the forum -->
                    <h1><a href="/forums/?forum_id=<?php echo $forum['forum_id']; ?>" class="forum-title-link">
                        <?php echo htmlspecialchars($forum['name']); ?>
                    </a></h1>

                    <!-- Display creation info: creator and creation date -->
                    <p class="forum-meta">
                        Created on <?php echo date("F j, Y", strtotime($forum['created_at'])); ?> by 
                        <a href="/account/profile?uid=<?php echo $forum['creator_id']; ?>" class="creator-link">
                            <?php echo htmlspecialchars($forum['creator_name']); ?>
                        </a>
                    </p>

                    <!-- Forum description -->
                    <p><?php echo htmlspecialchars($forum['description']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
<?php
include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
?>