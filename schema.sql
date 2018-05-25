CREATE DATABASE yeticave
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name CHAR(64) NOT NULL
);
CREATE UNIQUE INDEX i_category ON categories(name);

CREATE TABLE lots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    add_date DATETIME NOT NULL,
    name CHAR(64) NOT NULL,
    dscr VARCHAR(255) NOT NULL,
    img_url CHAR(128) NOT NULL,
    primary_price INT NOT NULL,
    end_date DATETIME NOT NULL,
    rate_step INT NOT NULL,
    author_id INT NOT NULL,
    winner_id INT,
    category_id INT NOT NULL
);
CREATE INDEX i_name ON lots(name);
CREATE INDEX i_dscr ON lots(dscr);

CREATE TABLE bet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bet_date DATETIME NOT NULL,
    amount INT NOT NULL,
    user_id INT NOT NULL,
    lot_id INT NOT NULL
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    add_date DATETIME NOT NULL,
    email CHAR(128) NOT NULL,
    name CHAR(64) NOT NULL,
    pass CHAR(128) NOT NULL,
    avatar_url CHAR(128),
    contacts CHAR(128)
);
CREATE UNIQUE INDEX i_email ON users(email);
