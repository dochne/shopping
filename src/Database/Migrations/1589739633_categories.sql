CREATE TABLE categories (
    id INTEGER PRIMARY KEY,
	name TEXT NOT NULL,
	position INTEGER NOT NULL,
	createdAt FLOAT NOT NULL,
	shopping TEXT NOT NULL DEFAULT ""
);

INSERT INTO categories
(name, position, createdAt)
VALUES
("Fruit and Veg", 1, 1589739633),
("Bread", 2, 1589739633),
("Snacks", 3, 1589739633),
("Drinks", 4, 1589739633),
("Cereal / Pasta", 5, 1589739633),
("Baking", 6, 1589739633),
("Canned Goods", 7, 1589739633),
("Toiletries", 8, 1589739633),
("Dairy", 9, 1589739633),
("Meat", 10, 1589739633),
("Frozen", 11, 1589739633);