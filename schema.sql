CREATE DATABASE yeticave
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name CHAR(64)
);
CREATE UNIQUE INDEX i_category ON categories(name);

CREATE TABLE lots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    add_date DATETIME,
    name CHAR(64),
    dscr VARCHAR(255),
    img_url CHAR(128),
	 primary_price DECIMAL(10,2),
	 end_date DATETIME,
	 rate_step DECIMAL(10,2),
	 author_id INT,
    winner_id INT,
    category_id INT
);
CREATE INDEX i_name ON lots(name);
CREATE INDEX i_dscr ON lots(dscr);

CREATE TABLE bet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bet_date DATETIME,
    amount DECIMAL(10,2),
    user_id INT,
    lot_id INT
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email CHAR(128),
    name CHAR(64),
    pass CHAR(128),
    avatar_url CHAR(128),
    contacts CHAR(128),
    created_lots_id INT,
    bet_id INT
);
CREATE UNIQUE INDEX i_email ON users(email);
