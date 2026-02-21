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
         header("Location: confirmation.php");
         exit;
      } else {
            header("Location: error.php?code=400&message=Invalid+input+data");
            exit;
      }
   } else {
      http_response_code(403);
      echo "There was a problem with your submission, please try again.";
   }
?>