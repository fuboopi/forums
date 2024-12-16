<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/setup/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true); 
    $email = $data['email'];
    if ($data) {
        function generateToken($length = 32) {
            return bin2hex(random_bytes($length / 2));
        }
        $token = generateToken();
        $query = "UPDATE users SET email_verify_token = ? WHERE uid = ?";
        if ($stmt = $link->prepare($query)) {
            $stmt->bind_param('si', $token, $_SESSION['uid']);
            $stmt->execute();
        };
        $to = $email;
        $subject = 'Verify Email';
        $message = $token;
        $headers = "From: no-reply@fuboopi.com" . "\r\n" .
                    "Reply-To: $email" . "\r\n" . 
                    "X-Mailer: PHP/" . phpversion();
        mail($to, $subject, $message, $headers);
        if (mail($to, $subject, $message, $headers)) {
            echo "Email sent successfully.";
        } else {
            echo "Failed to send email.";
        }
    };
};
?>