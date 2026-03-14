<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$name    = strip_tags(trim($_POST['name']    ?? ''));
$email   = filter_var(trim($_POST['email']   ?? ''), FILTER_SANITIZE_EMAIL);
$subject = strip_tags(trim($_POST['subject'] ?? ''));
$message = strip_tags(trim($_POST['message'] ?? ''));

if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$subject || !$message) {
    echo json_encode(['ok' => false, 'error' => 'invalid_fields']);
    exit;
}

$to      = 'info@radiobrazilweb.com';
$headers = "From: noreply@radiobrazilweb.com\r\n"
         . "Reply-To: $email\r\n"
         . "Content-Type: text/plain; charset=utf-8\r\n";
$body    = "Name: $name\nEmail: $email\n\nMessage:\n$message";

if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'error' => 'mail_failed']);
}
