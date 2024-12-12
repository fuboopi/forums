<?php

include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');


if (!isset($_SESSION['uid'])) {
    echo "You need to log in first.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $description = mysqli_real_escape_string($link, $_POST['description']);
    $created_by = $_SESSION['uid'];  

    $query = "INSERT INTO forums (name, description, created_by) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($link, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $name, $description, $created_by);

        if (mysqli_stmt_execute($stmt)) {
            $forumid = mysqli_insert_id($link);

            header("Location: /forums/?forum_id=$forumid");
            exit;
        } else {
            echo "Error: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($link);
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Forum | <?php echo $site_name?></title>
    <link rel="stylesheet" href="<?php echo $stylesheet ?>">
    <link rel="stylesheet" href="/style/header.css">
</head>
<body>
    <div class='container'>
        <!-- Forum creation form -->
        <form method="POST" action="create.php">
            <h1>Create a New Forum</h1>
            <label for="name">Forum Name:</label>
            <input type="text" name="name" id="name" required>

            <label for="description">Description:</label>
            <textarea name="description" id="description" required></textarea>

            <button type="submit">Create Forum</button>
        </form>
    </div>
</body>
</html>
