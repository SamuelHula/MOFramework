<nav class="admin-nav-bar">
   <a href="admin_dashboard.php" class="admin-nav-brand">Code Library Admin</a>
   
   <!-- Burger Menu for Mobile -->
   <input type="checkbox" id="admin_nav_toggle">
   <label for="admin_nav_toggle" class="admin-burger">
      <span></span>
      <span></span>
      <span></span>
   </label>
   
   <div class="admin-nav-menu">
      <a href="admin_dashboard.php" class="admin-nav-link <?php echo $active_page === 'dashboard' ? 'active' : ''; ?>">
         Dashboard
      </a>
      <?php if (isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'super_admin'): ?>
         <a href="manage_admin.php" class="admin-nav-link <?php echo $active_page === 'manage_admins' ? 'active' : ''; ?>">
            Manage Admins
         </a>
      <?php endif; ?>
      <a href="manage_users.php" class="admin-nav-link <?php echo $active_page === 'manage_users' ? 'active' : ''; ?>">
         Manage Users
      </a>
      <a href="admin_manage_snippets.php" class="admin-nav-link <?php echo $active_page === 'admin_manage_snippets' ? 'active' : ''; ?>">
         Manage Snippets
      </a>
      <form action="./assets/admin_logout.php" method="POST" style="display: inline;">
         <button type="submit" class="admin-signout-btn">Sign Out</button>
      </form>
   </div>
</nav>