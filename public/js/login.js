function validateEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
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

function initLoginForm() {
  const loginForm = document.getElementById('loginForm');
  const emailInput = document.getElementById('loginEmail');
  const passwordInput = document.getElementById('loginPassword');

  if (emailInput) {
    emailInput.addEventListener('input', () => {
      const email = emailInput.value.trim();
      const error =
        !validateEmail(email) && email.length > 0 ? 'Please enter a valid email address' : '';
      showError('loginEmailError', error);
    });
  }

  if (passwordInput) {
    passwordInput.addEventListener('input', () => {
      const password = passwordInput.value;
      const error = password.length > 0 && password.length < 1 ? 'Password is required' : '';
      showError('loginPasswordError', error);
    });
  }

  if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      clearErrors();

      const email = loginForm.email.value.trim();
      const password = loginForm.password.value;

      if (!validateEmail(email)) {
        showError('loginEmailError', 'Please enter a valid email address');
        return;
      }

      if (!password) {
        showError('loginPasswordError', 'Password is required');
        return;
      }

      showError('loginFormError', 'Processing...');

      try {
        const result = await api.login(email, password);
        showError('loginFormError', result.message);
        loginForm.classList.add('success');
        loginForm.reset();
        setTimeout(() => {
          window.location.href = '/home';
        }, 1000);
      } catch (error) {
        showError('loginFormError', error.message);
      }
    });
  }
}

document.addEventListener('DOMContentLoaded', async () => {
  await api.getCSRFToken();
  initPasswordToggle();
  initLoginForm();
});
