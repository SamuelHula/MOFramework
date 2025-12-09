<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include cookie functions
require_once 'cookie_functions.php';

// Auto-login from remember me cookie
if (!isset($_SESSION['loggedin']) && isset($_COOKIE['remember_me'])) {
    require_once 'config.php';
    
    list($user_id, $token) = explode(':', $_COOKIE['remember_me']);
    
    try {
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        
        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify token
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

// Check if cookie consent is needed
$needs_consent = needs_cookie_consent();

// If consent is needed and we're not on the consent page, redirect to consent
if ($needs_consent && $current_page !== 'cookie_consent.php') {
    // Store current URL to return after consent
    $_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
    header("Location: cookie_consent.php");
    exit;
}
?>
<nav id="nav_bar">
    <a href="index.php">
        <figure class="logo">
            <img src="" alt="Project Logo" title="Project Logo">
        </figure>
    </a>
    <input type="checkbox" id="nav_toggle">
    <label for="nav_toggle" class="burger">
        <span></span>
        <span></span>
        <span></span>
    </label>
    <ul class="nav_list">
        <li><a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>" title="Home">Home</a></li>
        
        <?php if ($current_page == 'index.php'): ?>
            <li><a href="#categories" title="Categories">Categories</a></li>
            <li><a href="#process" title="How It Works">How It Works</a></li>
            <li><a href="#faq" title="FAQ">FAQ</a></li>
            <li><a href="#contact_form" title="Contact">Contact</a></li>
        <?php else: ?>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <li><a href="account.php" class="<?php echo $current_page == 'account.php' ? 'active' : ''; ?>" title="Account Info">Account Info</a></li>
            <?php else: ?>
                <li><a href="about.php" class="<?php echo $current_page == 'about.php' ? 'active' : ''; ?>" title="About">About</a></li>
            <?php endif; ?>
            <li><a href="docs.php" class="<?php echo $current_page == 'docs.php' ? 'active' : ''; ?>" title="Documentation">Docs</a></li>   
            <li><a href="examples.php" class="<?php echo $current_page == 'examples.php' ? 'active' : ''; ?>" title="Examples">Examples</a></li>
        <?php endif; ?>
        
        <!-- Auth buttons -->
        <li class="nav-auth-buttons">
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <a href="dashboard.php" class="dashboard_btn" title="Dashboard">Dashboard</a>
                <a href="./assets/logout.php" class="signout_btn" title="Sign Out">Sign Out</a>
            <?php else: ?>
                <a href="signin.php" class="signin_btn" title="Sign In">Sign In</a>
                <a href="signup.php" class="signup_btn" title="Sign Up">Sign Up</a>
            <?php endif; ?>
            <?php if (isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true): ?>
                <li><a href="admin_dashboard.php" class="dashboard_btn">Admin Panel</a></li>
            <?php endif; ?>
        </li>
    </ul>
</nav>