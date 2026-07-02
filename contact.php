<?php
/**
 * ThemeForest Compliant Contact Form Handler
 * Powered by PHPMailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 1. Path to bundled PHPMailer files
require 'assets/php/PHPMailer/Exception.php';
require 'assets/php/PHPMailer/PHPMailer.php';
require 'assets/php/PHPMailer/SMTP.php';

header('Content-Type: application/json');

/* ============================================================
   CONFIGURATION (Tell your buyers to edit this section)
   ============================================================ */
define('RECIPIENT_EMAIL', 'yourmail@email.com'); // Where you want to receive emails
define('SENDER_NAME', 'My Personal Website');

// SETTING: Use 'smtp' for high reliability, or 'local' for standard server mail
define('MAIL_METHOD', 'smtp'); 

// SMTP Settings (Only used if MAIL_METHOD is 'smtp')
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'yourmail@email.com');
define('SMTP_PASS', 'spmr irxx fydm rtaz'); 
define('SMTP_PORT', 587);
define('SMTP_ENCRYPTION', 'tls'); // 'tls' or 'ssl'
/* ============================================================ */

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mail = new PHPMailer(true);

    try {
        // --- DATA COLLECTION ---
        $name  = isset($_POST['user_name']) ? strip_tags(trim($_POST['user_name'])) : 'Anonymous';
        $email = isset($_POST['user_email']) ? filter_var(trim($_POST['user_email']), FILTER_SANITIZE_EMAIL) : '';
        $msg   = isset($_POST['user_message']) ? strip_tags(trim($_POST['user_message'])) : '';

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please provide a valid email address.");
        }

        // --- MAILER SETUP ---
        if (MAIL_METHOD === 'smtp') {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = (SMTP_ENCRYPTION === 'tls') ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = SMTP_PORT;
            $mail->setFrom(SMTP_USER, SENDER_NAME);
        } else {
            // Standard local server mail
            $mail->isMail();
            // Best Practice: The 'From' should be an email belonging to your domain
            $mail->setFrom('noreply@' . $_SERVER['HTTP_HOST'], SENDER_NAME);
        }

        // --- EMAIL CONTENT ---
        $mail->addAddress(RECIPIENT_EMAIL);
        $mail->addReplyTo($email, $name); // Allows you to click 'Reply' in your inbox

        $mail->isHTML(true);
        $mail->Subject = "New Portfolio Message from: $name";
        
        // Modernized email body
        $mail->Body = "
            <div style='font-family: sans-serif; line-height: 1.6; color: #333;'>
                <h2>New Contact Inquiry</h2>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <hr style='border: 0; border-top: 1px solid #eee;'>
                <p><strong>Message:</strong></p>
                <p style='background: #f9f9f9; padding: 15px; border-radius: 5px;'>{$msg}</p>
            </div>
        ";

        $mail->send();
        echo json_encode(["status" => "success", "message" => "Message sent!"]);

    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $mail->ErrorInfo]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Direct access not allowed."]);
}