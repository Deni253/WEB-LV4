let isLoggedIn = false;

document.addEventListener('DOMContentLoaded', () => {
  fetch('check_login.php')
    .then(res => res.json())
    .then(data => {
      if (data.loggedIn) {
        isLoggedIn = true;
        loadImages();
      } else {
        alert('You must be logged in to rate images.');
        window.location.href = '/login.html';
      }
    })
    .catch(() => {
      alert('Session check failed.');
      window.location.href = '/login.html';
    });
});

function loadImages() {
  fetch('load_images.php')
    .then(res => {
      if (!res.ok) throw new Error('Failed to load images');
      return res.json();
    })
    .then(images => {
      console.log('Fetched images:', images);
      if (!Array.isArray(images)) {
        throw new Error('Invalid image data format');
      }

      const gallery = document.getElementById('imageGallery');
      gallery.innerHTML = '';

      images.forEach(image => {
        const col = document.createElement('div');
        col.className = 'col';

        col.innerHTML = `
          <div class="card h-100">
            <img src="${image.path}" class="card-img-top" alt="${image.filename}">
            <div class="card-body">
              <h5 class="card-title">${image.filename}</h5>
              <div class="rating mb-2" data-id="${image.id}">
                ${[1, 2, 3, 4, 5].map(i => `
                  <span class="star" data-value="${i}">&#9733;</span>
                `).join('')}
              </div>
              <div class="avg-rating text-muted">Average: <span class="avg">${image.avg_rating ? parseFloat(image.avg_rating).toFixed(1) : 'N/A'}</span></div>
            </div>
          </div>
        `;

        gallery.appendChild(col);
      });

      if (isLoggedIn) {
        document.querySelectorAll('.star').forEach(star => {
          star.addEventListener('click', () => {
            const rating = parseInt(star.dataset.value);
            const imageId = star.closest('.rating').dataset.id;

            fetch('rate_image.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ imageId, rating })
            })
              .then(res => res.json())
              .then(result => {
                if (result.message) {
                  alert(result.message);
                  loadImages();
                } else {
                  alert(result.error || 'Unknown error.');
                }
              })
              .catch(() => alert('Error saving rating.'));
          });
        });
      }
    })
    .catch((error) => {
      console.error('Image loading error:', error);
      const gallery = document.getElementById('imageGallery');
      gallery.innerHTML = '<div class="col"><div class="alert alert-danger">Error loading images.</div></div>';
    });
}

// Upload handler
const uploadForm = document.getElementById('uploadForm');
uploadForm.addEventListener('submit', async function (e) {
  e.preventDefault();

  const file = document.getElementById('imageFile').files[0];
  const message = document.getElementById('uploadMessage');

  message.textContent = '';

  if (!file) {
    message.textContent = 'Please select an image.';
    return;
  }
  if (!['image/jpeg', 'image/png'].includes(file.type)) {
    message.textContent = 'Only JPEG and PNG files are allowed.';
    return;
  }
  if (file.size > 5 * 1024 * 1024) {
    message.textContent = 'File size must not exceed 5MB.';
    return;
  }

  const formData = new FormData();
  formData.append('image', file);

  try {
    const response = await fetch('upload_image.php', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();
    if (response.ok) {
      alert(result.message);
      document.getElementById('uploadForm').reset();
      const modal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
      modal.hide();
      loadImages();
    } else {
      message.textContent = result.error || 'Upload failed.';
    }
  } catch (error) {
    message.textContent = 'Error uploading file.';
  }
});
