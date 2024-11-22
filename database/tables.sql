CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(60) UNIQUE NOT NULL,
    description TEXT,
    image VARCHAR(100) DEFAULT 'img/default.png',
    type ENUM('Bazar', 'Alimentacion', 'Limpieza'),
    stock INT NOT NULL DEFAULT 0
);
