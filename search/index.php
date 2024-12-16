<?php
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');

if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'] ?? '';
$searchType = $_GET['search_type'] ?? 'forums';

$query = "";
if ($searchType === 'forums') {
        $query = "SELECT * FROM forums WHERE name LIKE ? OR description LIKE ?";
    } else if ($searchType === 'users') {
        $query = "SELECT * FROM users WHERE name LIKE ? OR bio LIKE ?";
    }

    if ($query) {
        $stmt = mysqli_prepare($link, $query);

        if ($stmt) {
            $likeTerm = '%' . $searchTerm . '%';
            mysqli_stmt_bind_param($stmt, "ss", $likeTerm, $likeTerm);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            mysqli_stmt_close($stmt);
        } else {
            echo "Error: " . mysqli_error($link);
        }
    } else {
        echo "Invalid search type.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Search | <?php echo $site_name ?></title>
        <link rel="stylesheet" href="<?php echo $stylesheet; ?>">
        <link rel="stylesheet" href="/style/forums.css">
        <link rel="stylesheet" href="/style/search.css">
        <script src="/script/search/search.js"></script>
    </head>
    <body>
        <!-- Search !-->
        <div class="container">
            <h1>Search</h1>
            <form id="search_form" action="/search/" method="get">
                <div class="form-group">
                    <input type="text" name="search" id="search_box" required placeholder="Enter search term">
                    <input type="hidden" name="search_type" id="search_type" value="forums"> <!-- Hidden field -->
                    <div>
                        <h3>Search For:</h3>
                        <div class="search-itms">
                            <button type="submit" class="search-btn" id="search_forums">Forums</button>
                            <button type="submit" class="search-btn" id="search_users">Users</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>


        <!-- Results !-->
        <div class="container">
        <?php if (isset($result) && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="search-item">
                    <h3>
                        <a href="<?= $searchType === 'forums' ? '/forums/?forum_id=' . htmlspecialchars($row['forum_id']) : '/account/profile?uid=' . htmlspecialchars($row['uid']) ?>">
                            <?= htmlspecialchars($row['name']) ?>
                        </a>
                    </h3>
                    <p><?= $searchType === 'forums' ? htmlspecialchars($row['description']) : htmlspecialchars($row['bio']) ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No results found.</p>
        <?php endif; ?>
        </div>

    </body>
</html>

<?php
include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
?>
