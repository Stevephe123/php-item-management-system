USE inventory_system;

INSERT INTO categories (name) VALUES
('Laptop'),
('Desktop'),
('Mouse'),
('Keyboard');

-- admin@example.com / admin123
INSERT INTO users (name, email, password) VALUES
('Admin', 'admin@example.com',
'$2y$10$wH8m3sCq8P9V2NnD0Y1nBeF8kP9f1x7YzZy0HnQm8rYHkFqP8eZ2C');

INSERT INTO items (name, description, category_id, quantity) VALUES
('Dell XPS 13', 'Office laptop', 1, 5),
('Logitech MX Master', 'Wireless mouse', 3, 10);
