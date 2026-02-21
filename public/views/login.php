<?php
if (isset($_SESSION['user_id'])) {
    header('Location: /home');
    exit;
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="./styles/styles.css" />
    <title>Login - Secure App</title>
  </head>
  <body>
    <div class="bg-gradient"></div>
    <div class="bg-grid"></div>
    <div class="container">
      <h1>Secure Application</h1>

      <h2>Login</h2>
      <form id="loginForm" novalidate>
        <div class="form-group">
          <label for="loginEmail">Email</label>
          <input type="text" id="loginEmail" name="email" autocomplete="email" />
          <span class="error-message" id="loginEmailError"></span>
        </div>
        <div class="form-group">
          <label for="loginPassword">Password</label>
          <div class="password-input-wrapper">
            <input type="password" id="loginPassword" name="password" autocomplete="current-password" />
            <button type="button" class="toggle-password" data-target="loginPassword">
              <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <svg class="eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: none;">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
              </svg>
            </button>
          </div>
          <span class="error-message" id="loginPasswordError"></span>
        </div>
        <div class="error-message form-error" id="loginFormError"></div>
        <button type="submit">Login</button>
      </form>

      <p class="register-link">
        Don't have an account? <a href="/register">Register here</a>
      </p>
    </div>

    <script src="js/request.js"></script>
    <script src="js/login.js"></script>
  </body>
</html>
