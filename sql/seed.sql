USE inventory_system;

INSERT INTO categories (name) VALUES
('Laptop'),
('Desktop'),
('Mouse'),
('Keyboard');

-- admin@example.com / admin123
INSERT INTO users (name, email, password) VALUES
('Admin', 'admin@example.com',
'$2y$10$4rmlUTOBuvoA93SXnjIklOjg.57LhqbFUrtf3qdyd6ITOJHVnVeQC');

INSERT INTO items (name, description, category_id, quantity) VALUES
('Dell XPS 13', 'Office laptop', 1, 5),
('Logitech MX Master', 'Wireless mouse', 3, 10),   
('HP Pavilion Desktop', 'Home desktop computer', 2, 3),
('Mechanical Keyboard', 'RGB backlit keyboard', 4, 7);
