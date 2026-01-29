<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST["password"]);
    $remember = isset($_POST["remember"]) ? true : false;

    $errors = [];

    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id, first_name, last_name, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['loggedin'] = true;
                    
                    if ($remember) {
                        $cookie_value = $user['id'] . ':' . hash('sha256', $user['password']);
                        setcookie('remember_me', $cookie_value, time() + (30 * 24 * 60 * 60), '/'); 
                    }
                    
                    $log_entry = "=== SUCCESSFUL LOGIN ===" . PHP_EOL;
                    $log_entry .= "Time: " . date('Y-m-d H:i:s') . PHP_EOL;
                    $log_entry .= "User ID: " . $user['id'] . PHP_EOL;
                    $log_entry .= "Email: " . $email . PHP_EOL;
                    $log_entry .= "Remember: " . ($remember ? 'Yes' : 'No') . PHP_EOL;
                    $log_entry .= "IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
                    $log_entry .= "=================================" . PHP_EOL . PHP_EOL;
                    
                    file_put_contents('./assets/logs/auth.log', $log_entry, FILE_APPEND | LOCK_EX);
                    
                    header("Location: ../dashboard.php");
                    exit;
                } else {
                    $errors[] = "Invalid email or password";
                }
            } else {
                $errors[] = "Invalid email or password";
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $errors[] = "Login failed due to database error";
        }
    }

    if (!empty($errors)) {
        $errorString = implode("|", $errors);
        header("Location: ../signin.php?error=" . urlencode($errorString));
        exit;
    }
} else {
    header("Location: ../error.php?code=403&message=Invalid+request+method");
    exit;
}
?>