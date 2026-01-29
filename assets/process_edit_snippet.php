<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   header("Location: ../admin_signin.php");
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $snippet_id = intval($_POST['snippet_id']);
   $title = trim($_POST['title']);
   $description = trim($_POST['description']);
   $code = trim($_POST['code']);
   $language = trim($_POST['language']);
   $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
   $tags = isset($_POST['tags']) ? array_filter(explode(',', $_POST['tags'])) : [];
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
      $stmt = $pdo->prepare("SELECT id FROM snippets WHERE slug = ? AND id != ?");
      $stmt->execute([$slug, $snippet_id]);
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
               UPDATE snippets 
               SET title = ?, 
                  slug = ?, 
                  description = ?, 
                  code = ?, 
                  language = ?, 
                  category_id = ?, 
                  is_featured = ?, 
                  is_public = ?,
                  updated_at = NOW()
               WHERE id = ?
         ");
         
         $stmt->execute([
               $title,
               $slug,
               $description,
               $code,
               $language,
               $category_id,
               $is_featured,
               $is_public,
               $snippet_id
         ]);
         
         $stmt = $pdo->prepare("DELETE FROM snippet_tags WHERE snippet_id = ?");
         $stmt->execute([$snippet_id]);
         
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
         
         logAdminActivity($_SESSION['admin_id'], 'update_snippet', "Updated snippet: $title ($language)");
         
         header("Location: ../admin_edit_snippet.php?id=$snippet_id&success=Snippet+updated+successfully");
         exit;
         
      } catch (PDOException $e) {
         $pdo->rollBack();
         $errors[] = 'Database error: ' . $e->getMessage();
      }
   }
   
   if (!empty($errors)) {
      $errorString = implode('|', $errors);
      header("Location: ../admin_edit_snippet.php?id=$snippet_id&error=" . urlencode($errorString));
      exit;
   }
} else {
   header("Location: ../admin_dashboard.php");
   exit;
}
?>