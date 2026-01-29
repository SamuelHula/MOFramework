<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   header("Location: ../admin_signin.php");
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $title = trim($_POST['title']);
   $description = trim($_POST['description']);
   $code = trim($_POST['code']);
   $language = trim($_POST['language']);
   $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
   $tags = isset($_POST['tags']) ? explode(',', $_POST['tags']) : [];
   $is_featured = isset($_POST['is_featured']) ? 1 : 0;
   $is_public = isset($_POST['is_public']) ? 1 : 0;
   
   $errors = [];
   
   if (empty($title)) {
      $errors[] = 'Title is required';
   }
   
   if (empty($description)) {
      $errors[] = 'Description is required';
   }
   
   if (empty($code)) {
      $errors[] = 'Code is required';
   }
   
   if (empty($language)) {
      $errors[] = 'Language is required';
   }
   
   $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
   
   try {
      $stmt = $pdo->prepare("SELECT id FROM snippets WHERE slug = ?");
      $stmt->execute([$slug]);
      if ($stmt->fetch()) {
         $slug .= '-' . time();
      }
   } catch (PDOException $e) {
      $errors[] = 'Database error: ' . $e->getMessage();
   }
   
   if (empty($errors)) {
      try {
         $pdo->beginTransaction();
         
         $stmt = $pdo->prepare("
               INSERT INTO snippets 
               (title, slug, description, code, language, category_id, admin_id, is_featured, is_public) 
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
         ");
         
         $stmt->execute([
               $title,
               $slug,
               $description,
               $code,
               $language,
               $category_id,
               $_SESSION['admin_id'],
               $is_featured,
               $is_public
         ]);
         
         $snippet_id = $pdo->lastInsertId();
         
         if (!empty($tags)) {
               foreach ($tags as $tagName) {
                  $tagName = trim($tagName);
                  if (empty($tagName)) continue;
                  
                  $stmt = $pdo->prepare("SELECT id FROM tags WHERE name = ?");
                  $stmt->execute([$tagName]);
                  $tag = $stmt->fetch();
                  
                  if ($tag) {
                     $tag_id = $tag['id'];
                  } else {
                     $tagSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $tagName)));
                     $stmt = $pdo->prepare("INSERT INTO tags (name, slug) VALUES (?, ?)");
                     $stmt->execute([$tagName, $tagSlug]);
                     $tag_id = $pdo->lastInsertId();
                  }
                  
                  $stmt = $pdo->prepare("INSERT IGNORE INTO snippet_tags (snippet_id, tag_id) VALUES (?, ?)");
                  $stmt->execute([$snippet_id, $tag_id]);
               }
         }
         
         $pdo->commit();
         
         logAdminActivity($_SESSION['admin_id'], 'add_snippet', "Added new snippet: $title ($language)");
         
         header("Location: ../admin_dashboard.php?success=Snippet+added+successfully");
         exit;
         
      } catch (PDOException $e) {
         $pdo->rollBack();
         $errors[] = 'Database error: ' . $e->getMessage();
      }
   }
   
   if (!empty($errors)) {
      $errorString = implode('|', $errors);
      header("Location: ../admin_add_snippet.php?error=" . urlencode($errorString));
      exit;
   }
} else {
   header("Location: ../admin_dashboard.php");
   exit;
}