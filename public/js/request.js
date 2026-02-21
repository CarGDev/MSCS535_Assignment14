const API_BASE = 'api/index.php';

class ApiRequest {
  constructor() {
    this.csrfToken = null;
  }

  async getCSRFToken() {
    try {
      const formData = new FormData();
      formData.append('action', 'csrf_token');
      const response = await fetch(API_BASE, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
      });
      const data = await response.json();
      this.csrfToken = data.token;
      return this.csrfToken;
    } catch (error) {
      console.error('Failed to get CSRF token:', error);
      throw error;
    }
  }

  sanitizeHTML(str) {
    const temp = document.createElement('div');
    temp.textContent = str;
    return temp.innerHTML;
  }

  async request(action, formData) {
    if (!this.csrfToken) {
      await this.getCSRFToken();
    }

    formData.append('action', action);
    formData.append('csrf_token', this.csrfToken);

    try {
      const response = await fetch(API_BASE, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.error || 'Request failed');
      }

      if (data.error) {
        throw new Error(data.error);
      }

      return data;
    } catch (error) {
      if (error.message === 'Invalid CSRF token') {
        await this.getCSRFToken();
        formData.set('csrf_token', this.csrfToken);

        const response = await fetch(API_BASE, {
          method: 'POST',
          body: formData,
          credentials: 'same-origin',
        });
        const data = await response.json();

        if (!data.success) {
          throw new Error(data.error);
        }
        return data;
      }
      throw error;
    }
  }

  async create(username, email, password) {
    const formData = new FormData();
    formData.append('username', username);
    formData.append('email', email);
    formData.append('password', password);
    return this.request('create', formData);
  }

  async login(email, password) {
    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);
    return this.request('login', formData);
  }

  async submitData(data) {
    const formData = new FormData();
    formData.append('data', data);
    return this.request('submit_data', formData);
  }

  async getData() {
    const formData = new FormData();
    formData.append('action', 'get_data');
    try {
      const response = await fetch(API_BASE, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin',
      });
      return await response.json();
    } catch (error) {
      console.error('Failed to get data:', error);
      throw error;
    }
  }

  async deleteData(id) {
    const formData = new FormData();
    formData.append('id', id);
    return this.request('delete_data', formData);
  }
}

const api = new ApiRequest();
