<?php
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications | <?php echo $site_name?></title>
    <link rel="stylesheet" href="<?php echo $stylesheet; ?>">
    <link rel="stylesheet" href="/style/forums.css">

</head>
<body>
    <div class='container'>
        <h1>Recent Activity</h1>

    </div>
</body>
<?php
include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
?>