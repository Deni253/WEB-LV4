document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('movieForm');
  const messageBox = document.getElementById('message');

  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    const data = {
      title: document.getElementById('title').value,
      year: document.getElementById('year').value,
      duration: document.getElementById('duration').value,
      genre: document.getElementById('genre').value,
      country: document.getElementById('country').value,
      rating: document.getElementById('rating').value
    };

    try {
      const response = await fetch('add_movie.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });

      const result = await response.json();

      if (response.ok) {
        messageBox.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
        form.reset();
        if (typeof loadMovies === 'function') loadMovies({ showDelete: true }); 
      } else {
        messageBox.innerHTML = `<div class="alert alert-danger">${result.error || 'Failed to add movie.'}</div>`;
      }
    } catch (error) {
      console.error('Add error:', error);
      messageBox.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
    }
  });
});