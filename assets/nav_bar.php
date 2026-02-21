<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'cookie_functions.php';

if (!isset($_SESSION['loggedin']) && isset($_COOKIE['remember_me'])) {
    require_once 'config.php';
    
    list($user_id, $token) = explode(':', $_COOKIE['remember_me']);
    
    try {
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        
        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (hash('sha256', $user['password']) === $token) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['loggedin'] = true;
            }
        }
    } catch (PDOException $e) {
        error_log("Auto-login error: " . $e->getMessage());
    }
}

$current_page = basename($_SERVER['PHP_SELF']);

// Detect if we're in the tools subdirectory
$is_in_tools = strpos($_SERVER['PHP_SELF'], '/tools/') !== false;

// Set the base path for links based on current location
$base_path = $is_in_tools ? '../' : './';

$needs_consent = needs_cookie_consent();

if ($needs_consent && $current_page !== 'cookie_consent.php') {
    $_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
    header("Location: " . $base_path . "cookie_consent.php");
    exit;
}
?>
<nav id="nav_bar">
    <a href="<?php echo $base_path; ?>index.php" class="admin-nav-brand" style="color: white;">Code Library</a>
    <input type="checkbox" id="nav_toggle">
    <label for="nav_toggle" class="burger">
        <span></span>
        <span></span>
        <span></span>
    </label>
    <ul class="nav_list">
        <li><a href="<?php echo $base_path; ?>index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>" title="Home">Home</a></li>
        
        <?php if ($current_page == 'index.php' || $current_page == 'terms.php' || $current_page == 'cookie_policy.php' || $current_page == 'privacy.php' || $current_page == 'cookie_settings.php'): ?>
            <li><a href="index.php#categories" title="Categories">Categories</a></li>
            <li><a href="index.php#process" title="How It Works">How It Works</a></li>
            <li><a href="index.php#faq" title="FAQ">FAQ</a></li>
            <li><a href="index.php#contact_form" title="Contact">Contact</a></li>
        <?php else: ?>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <li><a href="<?php echo $base_path; ?>snippets_catalog.php" class="<?php echo $current_page == 'snippets_catalog.php' ? 'active' : ''; ?>" title="Code Snippets">Snippets</a></li>
                <li><a href="<?php echo $base_path; ?>favorites.php" class="<?php echo $current_page == 'favorites.php' ? 'active' : ''; ?>" title="My Favorites">Favorites</a></li>
                <li><a href="<?php echo $base_path; ?>web_tools.php" class="<?php echo ($current_page == 'web_tools.php' || $is_in_tools) ? 'active' : ''; ?>" title="Web Tools">Web Tools</a></li>
                <li><a href="<?php echo $base_path; ?>account.php" class="<?php echo $current_page == 'account.php' ? 'active' : ''; ?>" title="Account Info">Account</a></li>
            <?php endif; ?>
        <?php endif; ?>
        
        <li class="nav-auth-buttons">
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <a href="<?php echo $base_path; ?>dashboard.php" class="dashboard_btn" title="Dashboard">Dashboard</a>
                <a href="<?php echo $base_path; ?>assets/logout.php" class="signout_btn" title="Sign Out">Sign Out</a>
            <?php else: ?>
                <a href="<?php echo $base_path; ?>signin.php" class="signin_btn" title="Sign In">Sign In</a>
                <a href="<?php echo $base_path; ?>signup.php" class="signup_btn" title="Sign Up">Sign Up</a>
            <?php endif; ?>
        </li>
    </ul>
</nav>