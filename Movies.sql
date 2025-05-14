-- Ekstenzija za UUID generaciju
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- DROP tablice ako postojiš (ako želiš očistiti staro, makni komentar)
/*
DROP TABLE IF EXISTS "Image_Rating";
DROP TABLE IF EXISTS "User_Wishlist";
DROP TABLE IF EXISTS "Image";
DROP TABLE IF EXISTS "Movie";
DROP TABLE IF EXISTS "User";
*/

-- Tablica korisnika
CREATE TABLE "User" (
    "Id" UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    "Username" VARCHAR(255),
    "Password" VARCHAR(255),
    "Email" VARCHAR(255),
    "Name" VARCHAR(255),
    "Created_at" TIMESTAMP,
    "Role" VARCHAR(50) DEFAULT 'user'
);

-- Dodaj jednog admin korisnika
INSERT INTO "User" ("Id", "Username", "Password", "Email", "Name", "Role", "Created_at")
VALUES (
  gen_random_uuid(),
  'admin',
  crypt('secure', gen_salt('bf')),
  'admin@example.com',
  'Admin',
  'admin',
  NOW()
);

-- Tablica filmova
CREATE TABLE "Movie" (
    "Id" UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    "Title" VARCHAR(255) NOT NULL,
    "Year" INT NOT NULL,
    "Duration" INT NOT NULL,
    "Genre" VARCHAR(100) NOT NULL,
    "Rating" NUMERIC(2,1) CHECK ("Rating" >= 0 AND "Rating" <= 10),
    "Country" VARCHAR(100) NOT NULL DEFAULT 'Unknown',
    "Created_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tablica korisničke liste želja
CREATE TABLE "User_Wishlist" (
    "UserId" UUID NOT NULL,
    "MovieId" UUID NOT NULL,
    "Added_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY ("UserId", "MovieId"),
    FOREIGN KEY ("UserId") REFERENCES "User"("Id") ON DELETE CASCADE,
    FOREIGN KEY ("MovieId") REFERENCES "Movie"("Id") ON DELETE CASCADE
);

-- Tablica slika
CREATE TABLE "Image" (
  "Id" UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  "Filename" VARCHAR(255) NOT NULL,
  "Path" TEXT NOT NULL,
  "Source" VARCHAR(50),
  "Uploaded_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tablica ocjena slika
CREATE TABLE "Image_Rating" (
  "UserId" UUID REFERENCES "User"("Id") ON DELETE CASCADE,
  "ImageId" UUID REFERENCES "Image"("Id") ON DELETE CASCADE,
  "Rating" INT CHECK ("Rating" >= 1 AND "Rating" <= 5),
  "Rated_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY ("UserId", "ImageId")
);

-- Podaci za filmove
INSERT INTO "Movie" ("Title", "Year", "Duration", "Genre", "Rating", "Country") VALUES
('The Grand Escape', 2015, 120, 'Action', 7.8, 'USA'),
('Love in Paris', 2018, 105, 'Romance', 6.9, 'France'),
('Mystery Lake', 2020, 98, 'Thriller', 8.1, 'Canada'),
('Galaxy Raiders', 2022, 134, 'Sci-Fi', 4.3, 'USA'),
('The Forgotten Hero', 2010, 123, 'Drama', 8.3, 'UK'),
('Midnight Howl', 2019, 110, 'Horror', 3.6, 'Germany'),
('Laughing Stock', 2017, 95, 'Comedy', 7.0, 'USA'),
('Dance of Shadows', 2021, 102, 'Musical', 2.9, 'India'),
('Kingdoms Collide', 2016, 140, 'Fantasy', 8.0, 'New Zealand'),
('Urban Legends', 2014, 100, 'Mystery', 6.8, 'USA'),
('Coded Future', 2023, 112, 'Sci-Fi', 7.9, 'Japan'),
('Ocean Depths', 2012, 97, 'Adventure', 7.1, 'Australia'),
('Second Chance', 2011, 115, 'Drama', 6.7, 'USA'),
('The Final Note', 2018, 90, 'Biography', 4.8, 'UK'),
('Broken Mirror', 2016, 108, 'Thriller', 7.6, 'France'),
('After the Storm', 2020, 99, 'Drama', 8.2, 'USA'),
('Windswept', 2013, 87, 'Romance', 3.5, 'Italy'),
('Pixel War', 2019, 106, 'Animation', 7.3, 'South Korea'),
('The Archivist', 2017, 94, 'Documentary', 8.0, 'Germany'),
('Redemption Path', 2021, 121, 'Action', 4.1, 'Brazil');