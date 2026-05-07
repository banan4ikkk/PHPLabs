CREATE DATABASE IF NOT EXISTS bookhub
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE bookhub;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS genres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    author VARCHAR(100) NOT NULL,
    genre_id INT NOT NULL,
    year INT NOT NULL,
    description TEXT NOT NULL,
    status ENUM('available', 'unavailable') NOT NULL DEFAULT 'available',
    user_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO genres (name) VALUES
('Роман'),
('Фантастика'),
('Учебная литература'),
('Детектив'),
('История')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Пароль для admin: admin123
INSERT INTO users (username, email, password, role)
VALUES (
    'admin',
    'admin@example.com',
    '$2y$10$j0p9HLbAE0TJahqbNwEVu.FibX4ZAQ3UuLMmCI.zUYvCksU93RJ9.',
    'admin'
)
ON DUPLICATE KEY UPDATE email = email;

INSERT INTO books (title, author, genre_id, year, description, status, user_id) VALUES
('1984', 'George Orwell', 2, 1949, 'Антиутопический роман о контроле общества и свободе личности.', 'available', 1),
('Преступление и наказание', 'Фёдор Достоевский', 1, 1866, 'Классический роман о нравственном выборе, вине и наказании.', 'available', 1),
('PHP для начинающих', 'Иван Петров', 3, 2024, 'Учебное пособие по основам языка PHP и работе с базами данных.', 'available', 1)
ON DUPLICATE KEY UPDATE title = title;
