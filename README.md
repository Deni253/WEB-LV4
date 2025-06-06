# WEB-LV4
  Movie & Image Gallery Web App
  ### [Web-Site](https://striking-luck-production.up.railway.app/)
  ### IMPORTANT NOTE: YOU MUST REGISTER AND LOG IN FOR USER DASHBOARD TO APPEAR. ADMIN OPENS BY DEFAULT. WHEN YOU WANT TO ACCESS ADMIN DASHBOARD AGAIN LOG IN WITH USERNAME: `admin` AND PASSWORD: `secure`
  
  

A full-stack web application for managing and exploring a movie collection and image gallery. Built with PHP, PostgreSQL, JavaScript, and Bootstrap. Includes user authentication, role-based access (admin/user), filtering, image uploads with ratings, wishlist functionality, and Docker-based deployment.

---
## Features

### Movie Section
- Add, view, delete movies (admin only)
- Filter by **Genre**, **Year**, and **Country**
- Ratings displayed with conditional styling
- Users can **add movies to a wishlist**
- Admin dashboard at `index.html`, user dashboard at `User.html`
- Admin is manually inserted into the Database with the according role
- Admin dashboard opens by default
- To access Admin dashboard again log-in with Username: `admin` and password: `secure`

### Image Gallery
- Gallery accessible trough User dashboard only- button at the bottom of the page
- Responsive gallery with Bootstrap cards
- Upload images using the dropdown menu
- Rate images 1–5 stars (logged-in users only)
- Average rating shown per image
- Ratings persist in PostgreSQL

### Authentication
- User registration and login
- Passwords securely hashed
- Session-based login system
- Admin is redirected to `index.html`
- Regular users are redirected to `User.html`

### Technologies
- PHP (backend logic)
- PostgreSQL (database)
- Bootstrap 5 (frontend)
- JavaScript (fetch API, DOM handling)
- Docker (containerization)
- Railway (deployment)

---

## Database Schema (PostgreSQL)
- Movies database file included
