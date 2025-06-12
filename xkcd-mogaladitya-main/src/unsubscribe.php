<?php
require_once 'functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_STRING);

    if (isset($_POST['request_unsubscribe'])) {
        if ($email) {
            $verificationCode = generateVerificationCode();
            if (sendVerificationEmail($email, $verificationCode)) {
                $message = "A verification code has been sent to your email.";
                file_put_contents(__DIR__ . "/{$email}_unsubscribe_code.txt", $verificationCode);
            } else {
                $message = "Failed to send verification email. Please try again.";
            }
        } else {
            $message = "Invalid email address.";
        }
    } elseif (isset($_POST['confirm_unsubscribe'])) {
        if ($email && $code) {
            $storedCode = @file_get_contents(__DIR__ . "/{$email}_unsubscribe_code.txt");
            if ($storedCode && trim($storedCode) === $code) {
                if (unsubscribeEmail($email)) {
                    $message = "You have been successfully unsubscribed.";
                    unlink(__DIR__ . "/{$email}_unsubscribe_code.txt");
                } else {
                    $message = "This email is not registered.";
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
    <title>Unsubscribe</title>
</head>
<body>
    <h1>Unsubscribe</h1>
    <p><?php echo htmlspecialchars($message); ?></p>
    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br><br>
        <button type="submit" name="request_unsubscribe">Request Unsubscribe</button>
    </form>
    <br>
    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="code">Verification Code:</label>
        <input type="text" id="code" name="code" required>
        <br><br>
        <button type="submit" name="confirm_unsubscribe">Confirm Unsubscribe</button>
    </form>
</body>
</html>