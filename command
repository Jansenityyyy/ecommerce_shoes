-- Nike Table
CREATE TABLE nike (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    description TEXT
);

-- Adidas Table
CREATE TABLE adidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    description TEXT
);

-- Puma Table
CREATE TABLE puma (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    description TEXT
);



-- Nike Sample Products
INSERT INTO nike (name, price, image, description) VALUES
('Nike Mag "Back To The Future"', 15000, 'nike/Nike Mag.jpg', 'Iconic Nike Mag inspired by Back To The Future.'),
('Air Max 270', 8999, 'nike/Air Max 270.jpg', 'Lightweight and comfy.');

-- Adidas Sample Products
INSERT INTO adidas (name, price, image, description) VALUES
('Adidas Ultraboost', 8500, 'adidas/Ultraboost.jpg', 'Comfortable running shoes with boost technology.'),
('Adidas NMD R1', 7500, 'adidas/NMD R1.jpg', 'Modern lifestyle sneakers with primeknit.');

-- Puma Sample Products
INSERT INTO puma (name, price, image, description) VALUES
('Puma RS-X', 6000, 'puma/RS-X.jpg', 'Retro-inspired, bold design sneakers.'),
('Puma Suede Classic', 5000, 'puma/Suede Classic.jpg', 'Classic Puma style, everyday wear.');

git add.
git commit -m "Added product tables and sample data for Nike, Adidas, and Puma."
git push origin main