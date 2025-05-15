function loadMovies({ showDelete = false, showWishlist = false } = {}) {
  const genre = document.getElementById('filterGenre')?.value || '';
  const year = document.getElementById('filterYear')?.value || '';
  const country = document.getElementById('filterCountry')?.value || '';

  document.getElementById('applyFilters').addEventListener('click', () => {
  loadMovies({ showDelete: true }); 
});

document.getElementById('clearFilters').addEventListener('click', () => {
  document.getElementById('filterGenre').value = '';
  document.getElementById('filterYear').value = '';
  document.getElementById('filterCountry').value = '';
  loadMovies({ showDelete: true }); 
});


  const params = new URLSearchParams();
  if (genre) params.append('genre', genre);
  if (year) params.append('year', year);
  if (country) params.append('country', country);

  fetch('get_movies.php?' + params.toString())
    .then(response => response.json())
    .then(data => {
      const tableBody = document.getElementById('movieTableBody');
      tableBody.innerHTML = '';

      data.forEach(movie => {
        const row = document.createElement('tr');

        let rowHTML = `
          <td>${movie.Title}</td>
          <td>${movie.Year}</td>
          <td>${movie.Duration} min</td>
          <td>${movie.Genre}</td>
          <td>${movie.Country}</td>
          <td>${parseFloat(movie.Rating).toFixed(1) || 'N/A'}</td>
        `;

        if (showWishlist) {
          rowHTML += `
            <td>
              <button class="btn btn-success btn-sm wishlist-btn" data-id="${movie.Id}">Add</button>
            </td>`;
        }

        if (showDelete) {
          rowHTML += `
            <td>
              <button class="btn btn-danger btn-sm delete-btn" data-id="${movie.Id}">Delete</button>
            </td>`;
        }

        row.innerHTML = rowHTML;

        if (movie.Rating < 5) {
          row.classList.add('table-danger');
        }

        // Wishlist button handler
        if (showWishlist) {
          btn.addEventListener('click', () => {
          fetch('add_to_wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ movieId: movie.Id })
          })
            .then(res => res.json())
            .then(result => {
              if (result.warning) {
                const confirmAdd = confirm(result.warning);
                if (confirmAdd) {
                  fetch('add_to_wishlist.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ movieId: movie.Id, confirmLowRating: true })
                  })
                    .then(res2 => res2.json())
                    .then(result2 => alert(result2.message || result2.error))
                    .catch(() => alert('Failed to confirm add.'));
                }
              } else if (result.message) {
                alert(result.message);
              } else {
                alert(result.error || 'Something went wrong.');
              }
            })
            .catch(() => alert('Could not add to wishlist.'));
        });
        }

        // Delete button handler
        if (showDelete) {
          const delBtn = row.querySelector('.delete-btn');
          delBtn.addEventListener('click', () => {
            if (confirm('Are you sure you want to delete this movie?')) {
              fetch('delete_movie.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: movie.Id })
              })
                .then(res => res.json())
                .then(result => {
                  alert(result.message);
                  loadMovies({ showDelete, showWishlist });
                })
                .catch(() => alert('Error deleting movie.'));
            }
          });
        }

        tableBody.appendChild(row);
      });
    })
    .catch(() => {
      document.getElementById('movieTableBody').innerHTML =
        `<tr><td colspan="7" class="text-danger">Could not load movies.</td></tr>`;
    });
}