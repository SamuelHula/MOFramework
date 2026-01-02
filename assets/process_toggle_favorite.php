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
      header('Content-Type: application/json');
      echo json_encode(['success' => false, 'message' => 'Invalid snippet ID']);
      exit;
   }
   
   try {
      // Check if snippet exists and is public
      $stmt = $pdo->prepare("SELECT id FROM snippets WHERE id = ? AND is_public = 1");
      $stmt->execute([$snippet_id]);
      
      if (!$stmt->fetch()) {
         header('Content-Type: application/json');
         echo json_encode(['success' => false, 'message' => 'Snippet not found or not public']);
         exit;
      }
      
      // Check if already favorited
      $stmt = $pdo->prepare("SELECT id FROM user_favorites WHERE user_id = ? AND snippet_id = ?");
      $stmt->execute([$_SESSION['user_id'], $snippet_id]);
      $is_favorite = $stmt->fetch();
      
      if ($action === 'add' || $action === '') {
         if (!$is_favorite) {
            // Add to favorites
            $stmt = $pdo->prepare("INSERT INTO user_favorites (user_id, snippet_id) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $snippet_id]);
            $is_favorite = true;
         }
         header('Content-Type: application/json');
         echo json_encode(['success' => true, 'is_favorite' => true, 'message' => 'Added to favorites']);
         
      } elseif ($action === 'remove') {
         if ($is_favorite) {
            // Remove from favorites
            $stmt = $pdo->prepare("DELETE FROM user_favorites WHERE user_id = ? AND snippet_id = ?");
            $stmt->execute([$_SESSION['user_id'], $snippet_id]);
            $is_favorite = false;
         }
         header('Content-Type: application/json');
         echo json_encode(['success' => true, 'is_favorite' => false, 'message' => 'Removed from favorites']);
         
      } else {
         // Toggle action
         if ($is_favorite) {
            // Remove
            $stmt = $pdo->prepare("DELETE FROM user_favorites WHERE user_id = ? AND snippet_id = ?");
            $stmt->execute([$_SESSION['user_id'], $snippet_id]);
            $is_favorite = false;
         } else {
            // Add
            $stmt = $pdo->prepare("INSERT INTO user_favorites (user_id, snippet_id) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $snippet_id]);
            $is_favorite = true;
         }
         header('Content-Type: application/json');
         echo json_encode(['success' => true, 'is_favorite' => $is_favorite]);
      }
      
   } catch (PDOException $e) {
      error_log("Favorite toggle error: " . $e->getMessage());
      header('Content-Type: application/json');
      echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
   }
} else {
   header('Content-Type: application/json');
   echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>