<?php

/**
 * Generate a 6-digit numeric verification code.
 */
function generateVerificationCode(): string {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Send a verification code to an email.
 */
function sendVerificationEmail(string $email, string $code): bool {
    $subject = "Your Verification Code";
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
    $headers = "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com\r\n";

    return mail($email, $subject, $message, $headers);
}

/**
 * Register an email by storing it in a file.
 */
function registerEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

    if (!in_array($email, $emails)) {
        $emails[] = $email;
        return file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL) !== false;
    }

    return false; // Email already registered
}

/**
 * Unsubscribe an email by removing it from the list.
 */
function unsubscribeEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) {
        return false;
    }

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $updatedEmails = array_filter($emails, fn($e) => trim($e) !== $email);

    return file_put_contents($file, implode(PHP_EOL, $updatedEmails) . PHP_EOL) !== false;
}

/**
 * Fetch random XKCD comic and format data as HTML.
 */
function fetchAndFormatXKCDData(): string {
    $randomComicId = random_int(1, 2800); // XKCD has comics up to ~2800
    $url = "https://xkcd.com/$randomComicId/info.0.json";

    $response = file_get_contents($url);
    if ($response === false) {
        return '';
    }

    $comicData = json_decode($response, true);
    if (!$comicData) {
        return '';
    }

    $title = htmlspecialchars($comicData['title']);
    $img = htmlspecialchars($comicData['img']);
    $alt = htmlspecialchars($comicData['alt']);

    return "<h2>$title</h2><img src='$img' alt='$alt'><p>$alt</p>";
}

/**
 * Send the formatted XKCD updates to registered emails.
 */
function sendXKCDUpdatesToSubscribers(string $xkcdData): void {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) {
        error_log("No registered emails found.");
        return;
    }

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($emails as $email) {
        $subject = "Your XKCD Comic";
        $headers = "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: no-reply@example.com\r\n";

        if (!mail($email, $subject, $xkcdData, $headers)) {
            error_log("Failed to send email to $email");
        }
    }
}