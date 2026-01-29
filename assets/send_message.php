<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $name = strip_tags(trim($_POST["name"]));
      $name = str_replace(array("\r","\n"),array(" "," "),$name);
      $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
      $subject = trim($_POST["subject"]);
      $message = trim($_POST["message"]);

      if (empty($name) OR empty($subject) OR empty($message) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
         header("Location: error.php?code=400&message=Invalid+input+data");
         exit;
      }

      if (!is_dir('./assets/logs')) {
         mkdir('./assets/logs', 0755, true);
      }
      
      $log_entry = "=== NEW CONTACT FORM SUBMISSION ===" . PHP_EOL;
      $log_entry .= "Time: " . date('Y-m-d H:i:s') . PHP_EOL;
      $log_entry .= "Name: " . $name . PHP_EOL;
      $log_entry .= "Email: " . $email . PHP_EOL;
      $log_entry .= "Subject: " . $subject . PHP_EOL;
      $log_entry .= "Message: " . $message . PHP_EOL;
      $log_entry .= "IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
      $log_entry .= "=================================" . PHP_EOL . PHP_EOL;
      
      $log_success = file_put_contents(
         './assets/logs/contact_messages.log', 
         $log_entry, 
         FILE_APPEND | LOCK_EX
      );
      
      if ($log_success) {
         header("Location: confirmation.php?name=" . urlencode($name) . "&email=" . urlencode($email) . "&subject=" . urlencode($subject));
         exit;
      } else {
            header("Location: error.php?code=400&message=Invalid+input+data");
            exit;
      }

      /*
      ini_set("SMTP", "smtp.gmail.com");
      ini_set("smtp_port", "587");
      ini_set("sendmail_from", "your-email@gmail.com");
      ini_set("smtp_ssl", "tls");
      
      $to = "contact@mail.com";
      $email_subject = "New Contact Form: $subject";
      
      $email_content = "You have received a new message from your website contact form.\n\n";
      $email_content .= "Name: $name\n";
      $email_content .= "Email: $email\n";
      $email_content .= "Subject: $subject\n\n";
      $email_content .= "Message:\n$message\n";
      
      $headers = "From: your-email@gmail.com\r\n";
      $headers .= "Reply-To: $email\r\n";
      $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
      $headers .= "X-Mailer: PHP/" . phpversion();
      
      if (mail($to, $email_subject, $email_content, $headers)) {
         http_response_code(200);
         echo "Thank You! Your message has been sent.";
      } else {
         http_response_code(500);
         echo "Oops! Something went wrong and we couldn't send your message.";
         error_log("Email sending failed for: $email");
      }
      */

   } else {
      http_response_code(403);
      echo "There was a problem with your submission, please try again.";
   }
?>