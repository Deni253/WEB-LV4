<?php
header("Content-Type: application/json");

try {
    $pdo = new PDO("pgsql:host=localhost;dbname=postgres", "postgres", "postgres");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $genre = $_GET['genre'] ?? null;
    $year = $_GET['year'] ?? null;
    $country = $_GET['country'] ?? null;

    $query = 'SELECT "Id", "Title", "Year", "Duration", "Genre", "Country", "Rating" FROM "Movie" WHERE 1=1';
    $params = [];

    if (!empty($genre)) {
        $query .= ' AND LOWER("Genre") = LOWER(:genre)';
        $params[':genre'] = $genre;
    }
    if (!empty($year)) {
        $query .= ' AND "Year" = :year';
        $params[':year'] = (int)$year;
    }
    if (!empty($country)) {
        $query .= ' AND LOWER("Country") = LOWER(:country)';
        $params[':country'] = $country;
    }

    $query .= ' ORDER BY "Title" ASC';

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($movies);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}