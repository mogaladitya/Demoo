<?php
require_once 'functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);

    if (isset($_POST['register'])) {
        if ($email) {
            $verificationCode = generateVerificationCode();
            if (sendVerificationEmail($email, $verificationCode)) {
                $message = "A verification code has been sent to your email.";
                file_put_contents(__DIR__ . "/{$email}_code.txt", $verificationCode);
            } else {
                $message = "Failed to send verification email. Please try again.";
            }
        } else {
            $message = "Invalid email address.";
        }
    } elseif (isset($_POST['verify'])) {
        if ($email && $code) {
            $storedCode = @file_get_contents(__DIR__ . "/{$email}_code.txt");
            if ($storedCode && trim($storedCode) === $code) {
                if (registerEmail($email)) {
                    $message = "Your email has been successfully registered!";
                    unlink(__DIR__ . "/{$email}_code.txt");
                } else {
                    $message = "This email is already registered.";
                }
            } else {
                $message = "Invalid verification code.";
            }
        } else {
            $message = "Please provide both email and verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Registration</title>
</head>
<body>
    <h1>Email Registration</h1>
    <p><?php echo htmlspecialchars($message); ?></p>
    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br><br>
        <button type="submit" name="register">Register</button>
    </form>
    <br>
    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="code">Verification Code:</label>
        <input type="text" id="code" name="code" required>
        <br><br>
        <button type="submit" name="verify">Verify</button>
    </form>
</body>
</html>