<?php
// assets/process_toggle_favorite.php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
   header('Content-Type: application/json');
   echo json_encode(['success' => false, 'message' => 'Not logged in']);
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $snippet_id = isset($_POST['snippet_id']) ? intval($_POST['snippet_id']) : 0;
   $action = isset($_POST['action']) ? $_POST['action'] : '';
   
   if (!$snippet_id) {
      echo json_encode(['success' => false, 'message' => 'Invalid snippet ID']);
      exit;
   }
   
   try {
      // Check if snippet exists and is public
      $stmt = $pdo->prepare("SELECT id FROM snippets WHERE id = ? AND is_public = 1");
      $stmt->execute([$snippet_id]);
      
      if (!$stmt->fetch()) {
         echo json_encode(['success' => false, 'message' => 'Snippet not found']);
         exit;
      }
      
      if ($action === 'add') {
         // Add to favorites
         $stmt = $pdo->prepare("INSERT IGNORE INTO user_favorites (user_id, snippet_id) VALUES (?, ?)");
         $stmt->execute([$_SESSION['user_id'], $snippet_id]);
         
         echo json_encode(['success' => true, 'is_favorite' => true]);
         
      } elseif ($action === 'remove') {
         // Remove from favorites
         $stmt = $pdo->prepare("DELETE FROM user_favorites WHERE user_id = ? AND snippet_id = ?");
         $stmt->execute([$_SESSION['user_id'], $snippet_id]);
         
         echo json_encode(['success' => true, 'is_favorite' => false]);
         
      } else {
         // Toggle
         $stmt = $pdo->prepare("SELECT id FROM user_favorites WHERE user_id = ? AND snippet_id = ?");
         $stmt->execute([$_SESSION['user_id'], $snippet_id]);
         
         if ($stmt->fetch()) {
               // Remove
               $stmt = $pdo->prepare("DELETE FROM user_favorites WHERE user_id = ? AND snippet_id = ?");
               $stmt->execute([$_SESSION['user_id'], $snippet_id]);
               echo json_encode(['success' => true, 'is_favorite' => false]);
         } else {
               // Add
               $stmt = $pdo->prepare("INSERT INTO user_favorites (user_id, snippet_id) VALUES (?, ?)");
               $stmt->execute([$_SESSION['user_id'], $snippet_id]);
               echo json_encode(['success' => true, 'is_favorite' => true]);
         }
      }
      
   } catch (PDOException $e) {
      error_log("Favorite toggle error: " . $e->getMessage());
      echo json_encode(['success' => false, 'message' => 'Database error']);
   }
} else {
   echo json_encode(['success' => false, 'message' => 'Invalid request']);
}