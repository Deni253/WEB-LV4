
document.addEventListener('DOMContentLoaded', () => {
  const dropdown = document.querySelector('.dropdown');
  const wishlistDropdown = document.getElementById('wishlistDropdown');

  dropdown.addEventListener('show.bs.dropdown', () => {
    fetch('get_wishlist.php')
      .then(response => {
        if (!response.ok) throw new Error("Not logged in or server error");
        return response.json();
      })
      .then(data => {
        wishlistDropdown.innerHTML = '';
        if (data.length === 0) {
          wishlistDropdown.innerHTML = '<em class="text-muted">No movies added yet.</em>';
        } else {
          data.forEach(movie => {
            const item = document.createElement('div');
            item.className = 'small';
            item.textContent = `${movie.Title} (${movie.Year})`;
            wishlistDropdown.appendChild(item);
          });
        }
      })
      .catch(err => {
        wishlistDropdown.innerHTML = '<em class="text-danger">Could not load wishlist</em>';
      });
  });
});