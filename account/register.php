<?php
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');
include($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['register'])) {
        
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);

        
        if (empty($username) || empty($password) || empty($confirm_password)) {
            $error_message = "Please fill in all fields!";
        } elseif ($password !== $confirm_password) {
            
            $error_message = "3";
        } else {
            
            if (!preg_match("/^[a-zA-Z0-9._]{8,16}$/", $username)) {
                $error_message = "1";
            }
            
            elseif (!preg_match("/^(?=.*\d)[a-zA-Z0-9]{8,24}$/", $password)) {
                $error_message = "2";
            } else {
                
                $query = "SELECT * FROM users WHERE name = ? LIMIT 1";
                $stmt = $link->prepare($query); 
                $stmt->bind_param("s", $username);  
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $error_message = "Username already exists!";
                } else {
                    
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    
                    $insert_query = "INSERT INTO users (name, password) VALUES (?, ?)";
                    $stmt = $link->prepare($insert_query); 
                    $stmt->bind_param("ss", $username, $hashed_password);
                    $stmt->execute();

                    if ($stmt->affected_rows > 0) {
                        
                        $_SESSION['message'] = "Registration successful! Please login.";
                        header("Location: login");
                        exit();
                    } else {
                        $error_message = "There was an error during registration. Please try again.";
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | <?php echo $site_name?></title>
    <link rel="stylesheet" href="<?php echo $stylesheet ?>">
    <link rel="stylesheet" href="/style/header.css">
</head>
<body>
<form method="POST" action="register.php" class='container'>
<h2>Register</h2>
    <div class='warning' <?php if($error_message != "1"){echo "style='display: none;'";};?>>
        <h3>Username Must...</h3>
        <ul>
            <li>Be 8-16 Characters</li>
            <li>Not Include Spaces</li>
            <li>Not Include Symbols Except Underscores or Periods</li>
        </ul>
    </div>
    <div class='warning' <?php if($error_message != "2"){echo "style='display: none;'";};?> if>
        <h3>Password Must...</h3>
        <ul>
            <li>Be 8-24 Characters</li>
            <li>Includes At Least One Number</li>
        </ul>
    </div>
    <div class='warning' <?php if($error_message != "3"){echo "style='display: none;'";};?>>
        <h3>Passwords Do Not Match</h3>
    </div>
    <div class="form-group">
        <input type="text" name="username" placeholder="Username" required><br>
    </div>
    <div class="form-group">
        <input type="password" name="password" placeholder="Password" required><br>
    </div>
    <div class="form-group">
        <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
    </div>
    <br>
    <div class="form-group">
        <input type="checkbox" name="agree_tos" id="agree_tos" required>
        <label for="agree_tos">I agree to the <a href="/docs/tos" target="_blank">Terms of Service</a></label>
    </div>
    <br>
    <button type="submit" name="register">Register</button>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</form>
<?php
?>
</body>
</html>
<?php
include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php');
?>