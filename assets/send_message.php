<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Collect and sanitize input data
      $name = strip_tags(trim($_POST["name"]));
      $name = str_replace(array("\r","\n"),array(" "," "),$name);
      $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
      $subject = trim($_POST["subject"]);
      $message = trim($_POST["message"]);

      // Check that data was sent to the mailer
      if (empty($name) OR empty($subject) OR empty($message) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
         // Redirect to error page
         header("Location: error.php?code=400&message=Invalid+input+data");
         exit;
      }

      // =============================================
      // FILE LOGGING (ACTIVE - FOR TESTING)
      // =============================================
      
      // Create logs directory if it doesn't exist
      if (!is_dir('./assets/logs')) {
         mkdir('./assets/logs', 0755, true);
      }
      
      // Format the log entry
      $log_entry = "=== NEW CONTACT FORM SUBMISSION ===" . PHP_EOL;
      $log_entry .= "Time: " . date('Y-m-d H:i:s') . PHP_EOL;
      $log_entry .= "Name: " . $name . PHP_EOL;
      $log_entry .= "Email: " . $email . PHP_EOL;
      $log_entry .= "Subject: " . $subject . PHP_EOL;
      $log_entry .= "Message: " . $message . PHP_EOL;
      $log_entry .= "IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
      $log_entry .= "=================================" . PHP_EOL . PHP_EOL;
      
      // Append to log file
      $log_success = file_put_contents(
         './assets/logs/contact_messages.log', 
         $log_entry, 
         FILE_APPEND | LOCK_EX
      );
      
      if ($log_success) {
         // Redirect to confirmation page with data
         header("Location: confirmation.php?name=" . urlencode($name) . "&email=" . urlencode($email) . "&subject=" . urlencode($subject));
         exit;
      } else {
            // Redirect to error page
            header("Location: error.php?code=400&message=Invalid+input+data");
            exit;
      }

      // =============================================
      // EMAIL FUNCTIONALITY (COMMENTED OUT - READY TO USE)
      // =============================================
      /*
      // Uncomment the lines below to enable email sending
      // Make sure to configure your php.ini or use ini_set() for SMTP
      
      // Configure SMTP settings for Gmail
      ini_set("SMTP", "smtp.gmail.com");
      ini_set("smtp_port", "587");
      ini_set("sendmail_from", "your-email@gmail.com");
      ini_set("smtp_ssl", "tls");
      
      // Email details
      $to = "contact@mail.com";
      $email_subject = "New Contact Form: $subject";
      
      // Build email content
      $email_content = "You have received a new message from your website contact form.\n\n";
      $email_content .= "Name: $name\n";
      $email_content .= "Email: $email\n";
      $email_content .= "Subject: $subject\n\n";
      $email_content .= "Message:\n$message\n";
      
      // Build email headers
      $headers = "From: your-email@gmail.com\r\n";
      $headers .= "Reply-To: $email\r\n";
      $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
      $headers .= "X-Mailer: PHP/" . phpversion();
      
      // Send email
      if (mail($to, $email_subject, $email_content, $headers)) {
         http_response_code(200);
         echo "Thank You! Your message has been sent.";
      } else {
         http_response_code(500);
         echo "Oops! Something went wrong and we couldn't send your message.";
         // Log the error
         error_log("Email sending failed for: $email");
      }
      */

   } else {
      http_response_code(403);
      echo "There was a problem with your submission, please try again.";
   }
?>