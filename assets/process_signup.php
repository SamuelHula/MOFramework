<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = strip_tags(trim($_POST["firstName"]));
    $lastName = strip_tags(trim($_POST["lastName"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST["password"]);
    $confirmPassword = trim($_POST["confirmPassword"]);
    $agreeTerms = isset($_POST["agreeTerms"]) ? true : false;

    $errors = [];

    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        $errors[] = "All fields are required";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }

    if (!$agreeTerms) {
        $errors[] = "You must agree to the terms and conditions";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $errors[] = "Email already exists";
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $errors[] = "Database error occurred";
        }
    }

    if (empty($errors)) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$firstName, $lastName, $email, $hashedPassword]);
            
            if ($stmt->rowCount() > 0) {
                $userId = $pdo->lastInsertId();
                
                $_SESSION['user_id'] = $userId;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $firstName . ' ' . $lastName;
                $_SESSION['first_name'] = $firstName;
                $_SESSION['loggedin'] = true;
                
                $log_entry = "=== SUCCESSFUL REGISTRATION ===" . PHP_EOL;
                $log_entry .= "Time: " . date('Y-m-d H:i:s') . PHP_EOL;
                $log_entry .= "User ID: " . $userId . PHP_EOL;
                $log_entry .= "Name: " . $firstName . " " . $lastName . PHP_EOL;
                $log_entry .= "Email: " . $email . PHP_EOL;
                $log_entry .= "IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
                $log_entry .= "=================================" . PHP_EOL . PHP_EOL;
                
                file_put_contents('./assets/logs/auth.log', $log_entry, FILE_APPEND | LOCK_EX);
                
                header("Location: ../dashboard.php");
                exit;
            } else {
                $errors[] = "Registration failed";
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $errors[] = "Registration failed due to database error";
        }
    }

    if (!empty($errors)) {
        $errorString = implode("|", $errors);
        header("Location: ../signup.php?error=" . urlencode($errorString));
        exit;
    }
} else {
    header("Location: ../error.php?code=403&message=Invalid+request+method");
    exit;
}
?>