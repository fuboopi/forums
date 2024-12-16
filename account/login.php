<?php

include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['login'])) {
        
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $remember_me = isset($_POST['remember_me']) ? true : false;

        
        if (empty($username) || empty($password)) {
            $error_message = "Please fill in all fields!";
        } else {
            
            $query = "SELECT * FROM users WHERE name = ? LIMIT 1";
            $stmt = $link->prepare($query);  
            $stmt->bind_param("s", $username);  
            $stmt->execute();
            $result = $stmt->get_result();

            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                
                if (password_verify($password, $user['password'])) {
                    
                    $_SESSION['uid'] = $user['uid'];
                    $_SESSION['name'] = $user['name'];

                    
                    if ($remember_me && !$user['remember_token']) {
                        
                        $token = bin2hex(random_bytes(32));
                        
                        $update_query = "UPDATE users SET remember_token = ? WHERE uid = ?";
                        $stmt = $link->prepare($update_query);
                        $stmt->bind_param("si", $token, $user['uid']);
                        $stmt->execute();

                        
                        setcookie("remember_me", $token, time() + (30 * 24 * 60 * 60), "/");  
                    } else {
                        $token = $user['remember_token'];
                        setcookie("remember_me", $token, time() + (30 * 24 * 60 * 60), "/");
                    }

                    header("Location: /");
                    exit();
                } else {
                    $error_message = "Invalid password!";
                }
            } else {
                $error_message = "User not found!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | <?php echo $site_name?></title>
    <link rel="stylesheet" href="<?php echo $stylesheet ?>">
</head>
<body>

<div class="login-container">
    <form method="POST" action="login.php" class='container'>
        <h2>Login</h2>
        <?php
        if (isset($error_message)) {
            echo "<h3 class='warning' style='text-align: center; margin: 0 auto;'>$error_message</h3>";
        }
        ?>
        <div class="form-group">
            <input type="text" name="username" placeholder="Username" required class="input-field"><br>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required class="input-field"><br>
        </div>
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="remember_me"> Remember Me
            </label><br>
        </div>
        <div class="form-group">
            <button type="submit" name="login" class="btn-submit">Login</button>
        </div>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </form>
</div>

</body>
</html>
<?php
include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
?>