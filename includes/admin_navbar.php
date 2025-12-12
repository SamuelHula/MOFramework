<nav class="admin-nav-bar">
   <a href="admin_dashboard.php" class="admin-nav-brand">Code Library Admin</a>
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
      <a href="#" class="admin-nav-link <?php echo $active_page === 'settings' ? 'active' : ''; ?>">
         Settings
      </a>
      <form action="./assets/admin_logout.php" method="POST" style="display: inline;">
         <button type="submit" class="admin-signout-btn">Sign Out</button>
      </form>
   </div>
</nav>