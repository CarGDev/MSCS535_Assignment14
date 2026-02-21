<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$username = $_SESSION['username'] ?? 'User';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="./styles/styles.css" />
    <title>Home - Secure App</title>
  </head>
  <body>
    <div class="bg-gradient"></div>
    <div class="bg-grid"></div>

    <header class="header">
      <div class="logo">
        <span class="logo-icon">🔐</span>
        <span class="logo-text">SecureVault</span>
      </div>
      <a href="/logout" class="btn-logout">
        <span>Logout</span>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
          <polyline points="16,17 21,12 16,7" />
          <line x1="21" y1="12" x2="9" y2="12" />
        </svg>
      </a>
    </header>

    <main class="main">
      <div class="hero">
        <div class="hero-badge">Welcome Back</div>
        <h1 class="hero-title">
          Hello, <span class="gradient-text"><?php echo htmlspecialchars($username); ?></span>
        </h1>
        <p class="hero-subtitle">Your secure space awaits. Manage your data with confidence.</p>
      </div>

      <div class="card">
        <div class="card-header">
          <h2 class="card-title">Submit Data</h2>
          <p class="card-description">Store your information securely</p>
        </div>
        <form id="dataForm" class="data-form">
          <div class="input-group">
            <label for="dataInput">Your Data</label>
            <textarea id="dataInput" name="data" rows="4" placeholder="Enter your data here..." required></textarea>
          </div>
          <button type="submit" class="btn-primary">
            <span>Submit Data</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="22" y1="2" x2="11" y2="13" />
              <polygon points="22,2 15,22 11,13 2,9" />
            </svg>
          </button>
        </form>
      </div>

      <div class="card">
        <div class="card-header">
          <h2 class="card-title">Your Data</h2>
          <p class="card-description">All your stored information</p>
        </div>
        <div class="data-list" id="dataList">
          <p class="empty-state">No data submitted yet</p>
        </div>
      </div>
    </main>

    <div id="message"></div>

    <script src="js/request.js"></script>
    <script src="js/home.js"></script>
  </body>
</html>
