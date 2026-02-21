async function loadUserData() {
  const dataList = document.getElementById('dataList');
  if (!dataList) return;

  try {
    const result = await api.getData();
    if (result.success) {
      if (result.data.length === 0) {
        dataList.innerHTML = '<p class="empty-state">No data submitted yet</p>';
      } else {
        dataList.innerHTML = result.data
          .map(
            (item) => `
            <div class="data-item">
              <div class="data-item-left">
                <span class="data-item-content">${api.sanitizeHTML(item.data)}</span>
                <span class="data-item-time">${new Date(item.created_at).toLocaleString()}</span>
              </div>
              <button class="btn-delete" data-id="${item.id}" title="Delete">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="color: white;">
                  <path d="M10 2L9 3L3 3L3 5L4.109375 5L5.8925781 20.255859L5.8925781 20.263672C6.023602 21.250335 6.8803207 22 7.875 22L16.123047 22C17.117726 22 17.974445 21.250322 18.105469 20.263672L18.107422 20.255859L19.890625 5L21 5L21 3L15 3L14 2L10 2zM6.125 5L17.875 5L16.123047 20L7.875 20L6.125 5z"></path>
                </svg>
              </button>
            </div>`
          )
          .join('');

        document.querySelectorAll('.btn-delete').forEach((btn) => {
          btn.addEventListener('click', async (e) => {
            const id = e.currentTarget.dataset.id;
            try {
              await api.deleteData(id);
              loadUserData();
            } catch (error) {
              console.error('Failed to delete:', error);
            }
          });
        });
      }
    }
  } catch (error) {
    console.error('Failed to load data:', error);
  }
}

function initDataForm() {
  const dataForm = document.getElementById('dataForm');
  const messageDiv = document.getElementById('message');

  if (dataForm) {
    dataForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      messageDiv.textContent = 'Processing...';
      messageDiv.className = 'message info';

      const data = dataForm.data.value.trim();

      try {
        const result = await api.submitData(data);
        messageDiv.textContent = result.message;
        messageDiv.className = 'message success';
        dataForm.reset();
        loadUserData();
      } catch (error) {
        messageDiv.textContent = error.message;
        messageDiv.className = 'message error';
      }
    });
  }
}

document.addEventListener('DOMContentLoaded', async () => {
  await api.getCSRFToken();
  initDataForm();
  loadUserData();
});
