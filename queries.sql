USE yeticave;

INSERT INTO categories (name)
VALUES ('Доски и лыжи'),
        ('Крепления'),
        ('Ботинки'),
        ('Одежда'),
        ('Инструменты'),
        ('Разное');

INSERT INTO users (email, name, pass, avatar_url, contacts)
VALUES ('panenko231@mail.ru', 'Виктор Паненко',  'pan321', 'img/pan321.png', '903-321-45-32'),
       ('golikov123@mail.ru', 'Дмитрий Голиков', 'gol123', 'img/gol123.png', '925-322-32-32'),
       ('pozvonit@mail.ru',   'Аристарх Займ',   'zaim',   'img/zaim.png',   '800-555-35-35');

INSERT INTO lots (add_date, name, dscr, img_url, primary_price, end_date, rate_step, author_id, category_id)
VALUES ('2018-04-02 12:00:30', '2014 Rossignol District Snowboard',                 'Very good snowboard', 'img/lot-1.jpg', '10999',  '2018-06-02 12:00:30', '100', '2', '1'),
       ('2018-04-03 13:00:30', 'DC Ply Mens 2016/2017 Snowboard',                   'great board',         'img/lot-2.jpg', '15999',  '2018-06-03 13:00:30', '200', '1', '1'),
       ('2018-05-10 20:00:00', 'Крепления Union Contact Pro 2015 года размер L/XL', 'good stuff',          'img/lot-3.jpg', '8000',   '2018-07-10 20:00:00', '150', '3', '2'),
       ('2018-05-11 19:05:00', 'Ботинки для сноуборда DC Mutiny Charocal',          'boots',               'img/lot-4.jpg', '10999',  '2018-07-11 19:05:00', '120', '1', '3'),
       ('2018-01-11 19:05:00', 'Куртка для сноуборда DC Mutiny Charocal',           'jacket',              'img/lot-5.jpg', '7500',   '2018-03-11 19:05:00', '300', '2', '4'),
       ('2018-03-20 21:15:00', 'Маска Oakley Canopy',                               'helmet',              'img/lot-6.jpg', '5400',   '2018-05-20 21:15:00', '250', '2', '6');

INSERT INTO bet (bet_date, amount, user_id, lot_id)
VALUES ('2018-04-02 12:10:30', '11099', '1', '1'),
       ('2018-04-02 12:15:40', '11199', '3', '1'),
       ('2018-04-02 12:30:45', '11299', '1', '1'),
       ('2018-04-02 13:34:45', '15199', '1', '2');

-- получить все категории:

SELECT name FROM categories
ORDER BY id;

/*
  получить самые новые, открытые лоты. Каждый лот должен включать название,
  стартовую цену, ссылку на изображение, цену, количество ставок, название категории:
*/

SELECT l.name, l.primary_price, l.img_url, MAX(b.amount) AS max_bet, COUNT(b.lot_id) AS bet_count, c.name
FROM lots l
LEFT JOIN bet b
ON b.lot_id = l.id
JOIN categories c
ON l.category_id = c.id
WHERE NOW() < l.end_date
GROUP BY l.id
ORDER BY l.add_date DESC;

-- показать лот по его id. Получите также название категории, к которой принадлежит лот:

SELECT l.name, l.primary_price, l.img_url, c.name
FROM lots l
JOIN categories c
ON l.category_id = c.id
WHERE l.id = '2';

-- обновить название лота по его идентификатору:

UPDATE lots
SET name ='Маска Джейсона'
WHERE id = '6';

-- получить список самых свежих ставок для лота по его идентификатору:

SELECT bet_date, amount
FROM bet b
WHERE lot_id = 1
ORDER BY bet_date DESC;
