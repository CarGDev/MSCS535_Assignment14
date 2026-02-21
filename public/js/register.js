function validateEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

function validatePassword(password) {
  if (password.length === 0) return '';
  if (password.length < 8) return 'Password must be at least 8 characters';
  if (password.length > 128) return 'Password must be less than 128 characters';
  if (!/[a-z]/.test(password)) return 'Password must contain at least one lowercase letter';
  if (!/[A-Z]/.test(password)) return 'Password must contain at least one uppercase letter';
  if (!/[0-9]/.test(password)) return 'Password must contain at least one number';
  if (!/[!@#$%^&*(),.?":{}|<>]/.test(password))
    return 'Password must contain at least one special character';
  return '';
}

function validateUsername(username) {
  if (username.length === 0) return '';
  if (username.length < 3) return 'Username must be at least 3 characters';
  return '';
}

function showError(elementId, message) {
  const errorEl = document.getElementById(elementId);
  if (errorEl) {
    errorEl.textContent = message;
    errorEl.style.display = message ? 'block' : 'none';
  }
}

function clearErrors() {
  document.querySelectorAll('.error-message').forEach((el) => {
    el.textContent = '';
    el.style.display = 'none';
  });
}

function initPasswordToggle() {
  document.querySelectorAll('.toggle-password').forEach((btn) => {
    btn.addEventListener('click', () => {
      const targetId = btn.dataset.target;
      const input = document.getElementById(targetId);
      if (input) {
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        btn.querySelector('.eye-open').style.display = isPassword ? 'none' : 'block';
        btn.querySelector('.eye-closed').style.display = isPassword ? 'block' : 'none';
      }
    });
  });
}

function initRegisterForm() {
  const registerForm = document.getElementById('registerForm');
  const usernameInput = document.getElementById('regUsername');
  const emailInput = document.getElementById('regEmail');
  const passwordInput = document.getElementById('regPassword');

  if (usernameInput) {
    usernameInput.addEventListener('input', () => {
      const username = usernameInput.value.trim();
      const error = validateUsername(username);
      showError('regUsernameError', error);
    });
  }

  if (emailInput) {
    emailInput.addEventListener('input', () => {
      const email = emailInput.value.trim();
      const error =
        !validateEmail(email) && email.length > 0 ? 'Please enter a valid email address' : '';
      showError('regEmailError', error);
    });
  }

  if (passwordInput) {
    passwordInput.addEventListener('input', () => {
      const password = passwordInput.value;
      const error = validatePassword(password);
      showError('regPasswordError', error);
    });
  }

  if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      clearErrors();

      const username = registerForm.username.value.trim();
      const email = registerForm.email.value.trim();
      const password = registerForm.password.value;

      const usernameError = validateUsername(username);
      if (usernameError) {
        showError('regUsernameError', usernameError);
        return;
      }

      if (!validateEmail(email)) {
        showError('regEmailError', 'Please enter a valid email address');
        return;
      }

      const passwordError = validatePassword(password);
      if (passwordError) {
        showError('regPasswordError', passwordError);
        return;
      }

      showError('registerFormError', 'Processing...');

      try {
        const result = await api.create(username, email, password);
        showError('registerFormError', result.message);
        registerForm.classList.add('success');
        registerForm.reset();
        setTimeout(() => {
          window.location.href = '/login';
        }, 1500);
      } catch (error) {
        showError('registerFormError', error.message);
      }
    });
  }
}

document.addEventListener('DOMContentLoaded', async () => {
  await api.getCSRFToken();
  initPasswordToggle();
  initRegisterForm();
});
