<?php
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');


$sql = "SELECT * FROM forums ORDER BY created_at DESC LIMIT 2"; 
$result = mysqli_query($link, $sql);
$featured_topics = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home | <?php echo htmlspecialchars($site_name); ?></title>
    <link rel="stylesheet" href="<?php echo $stylesheet ?>">
    <link rel="stylesheet" href="/style/header.css">
</head>
<body>
    <!-- Main -->
    <section id="hero" class='container'>
        <div class="hero-content">
            <h1 class="title">Welcome to <?php echo htmlspecialchars($site_name); ?></h1>
            <p>:3</p>
            <!-- <a href="/forums/latest" class="cta-btn">Start Exploring</a> !-->
        </div>
    </section>

    <!-- Featured Forums -->
    <section id="featured" class='container'>
        <h2>Featured Forums</h2>
        <div class="featured-content">
            <?php foreach ($featured_topics as $topic): ?>
                <div class="topic">
                    <h3><a href="/forums/?forum_id=<?php echo $topic['forum_id']; ?>" class='forum-btn'><?php echo htmlspecialchars($topic['name']); ?></a></h3>
                    <p><?php echo htmlspecialchars(substr($topic['description'], 0, 100)); ?>...</p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="quick-link">
            <a href="/forums/latest" class="cta-btn">Browse Forums</a>
        </div>
    </section>

    <!-- Quick Links -->
    <section id="quick-links" class='container'>
        <h2> Quick Links</h2>
        <div class="quick-link">
            <h3>DMs</h3>
            <a href="/messages" class="cta-btn">Check Messages</a>
        </div>
    </section>

</body>
</html>

<?php
include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
?>
