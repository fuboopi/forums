<html>
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/style/header.css">
  <link href="/fonts/fontawesome/css/fontawesome.css" rel="stylesheet" />
  <link href="/fonts/fontawesome/css/brands.css" rel="stylesheet" />
  <link href="/fonts/fontawesome/css/solid.css" rel="stylesheet" />
    </head>
</html>

<header>
    <div class="navbar">
        <a href="/"><i class="fa-regular fa-house"></i></a>
        <a href="/forums/latest" class='navbar-link'>Forums</a>
        <a href="/messages" class='navbar-link'><i class="fa-regular fa-message"></i></a>

        <div class="navbar-right">
        <a href="/search"><i class="fa-regular fa-magnifying-glass"></i></a>
        <!--<a href="/account/notifications" class='navbar-link'><i class="fa-regular fa-bell"></i></a>!-->
            <?php if (isset($_SESSION['name'])): ?>
                <div class="dropdown">
                    <button class="dropbtn-mobile"><i class="fa-solid fa-bars"></i></button>
                    <button class="dropbtn"><?php echo htmlspecialchars($_SESSION['name']); ?></button>
                    <div class="dropdown-content">
                        <a href="/account/profile">My Profile</a>
                        <a href="/forums/latest" class='mobile-only'>Forums</a>
                        <a href="/messages" class='mobile-only'>Messages</a>
                        <!--<a href="/account/notifications" class='mobile-only'>Notifications</a>!-->
                        <a href="/account/logout">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/account/login" class="login-register-btn">Login / Register</a>
            <?php endif; ?>
        </div>
    </div>
</header>
