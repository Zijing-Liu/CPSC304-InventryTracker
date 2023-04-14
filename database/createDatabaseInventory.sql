DROP VIEW All_quantity;
DROP VIEW in_store;
DROP VIEW in_transit;
DROP VIEW in_warehouse;
DROP VIEW location_quantity;


DROP DATABASE IF EXISTS `Inventory`;
CREATE DATABASE `Inventory`;
USE `Inventory`;

-- drop table if exists Product cascade;
-- drop table if exists Supplies cascade;
-- drop table if exists Houses cascade;
-- drop table if exists Has cascade;
-- drop table if exists Location_R1 cascade;
-- drop table if exists Location_R3 cascade;
-- drop table if exists Location_R4 cascade;
-- drop table if exists Store cascade;
-- drop table if exists Warehouse cascade;
-- drop table if exists Package cascade;
-- drop table if exists Accesses cascade;
-- drop table if exists Internal_Fleet cascade;
-- drop table if exists External_Company cascade;
-- drop table if exists External_Fleet cascade;
-- drop table if exists Travels_to cascade;
-- drop table if exists Company cascade;
-- drop table if exists Vehicle_type cascade;
-- drop table if exists Transportation cascade;



CREATE TABLE Product(
	product_code INTEGER PRIMARY KEY,
	product_name CHAR(20) NOT NULL,
	company_name CHAR(30) NOT NULL
);

CREATE TABLE Location_R1(
	address CHAR(100) PRIMARY KEY,
	area_code INTEGER
);

CREATE TABLE Location_R3(
	delivery_hours_start TIME,
	delivery_hours_end TIME,	
	delivery_hours_length INTEGER,
	PRIMARY KEY (delivery_hours_start, delivery_hours_end)
);

CREATE TABLE Company(
	company_name CHAR(30),
	PRIMARY KEY (company_name)
);
-- R4(address, location_id, location_name, phone_number, delivery_hours_start, delivery_hours_end, capacity, company_name)
CREATE TABLE Location_R4(
	location_id INT,
	address CHAR(100) NOT NULL,
	location_name CHAR(30) NOT NULL,
	phone_number INT,
	delivery_hours_start TIME,
	delivery_hours_end TIME,
	capacity INT NOT NULL,
    company_name CHAR(30),
	PRIMARY KEY (location_id),
	UNIQUE (address, location_name), 
	FOREIGN KEY (company_name) REFERENCES Company (company_name) ON DELETE CASCADE ON UPDATE CASCADE 
    
);

CREATE TABLE Store(
	location_id INTEGER PRIMARY KEY,
	opening_hours_start TIME,
	opening_hours_end TIME,
	FOREIGN KEY (location_id) REFERENCES Location_R4 (location_id) ON DELETE CASCADE ON UPDATE CASCADE 
);


CREATE TABLE Warehouse(
	location_id INTEGER PRIMARY KEY,
	num_loading_docks INTEGER,
	FOREIGN KEY (location_id) REFERENCES Location_R4 (location_id) ON DELETE CASCADE ON UPDATE CASCADE 
	
);

CREATE TABLE Vehicle_type(
	type_name CHAR(30) PRIMARY KEY,
	capacity INTEGER
);

CREATE TABLE Transportation(
	license_plate CHAR(7),
	type_name CHAR(30),
	PRIMARY KEY (license_plate),
	FOREIGN KEY (type_name) REFERENCES Vehicle_type (type_name) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE Package(
	package_id INTEGER PRIMARY KEY,
	destination INTEGER NOT NULL,
	location_id INTEGER,
	license_plate CHAR(7), 
	FOREIGN KEY (location_id) REFERENCES Location_R4 (location_id) ON DELETE SET NULL ON UPDATE CASCADE,
	FOREIGN KEY (license_plate) REFERENCES Transportation (license_plate) ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE Supplies(
	product_code INTEGER NOT NULL,
	location_id  INTEGER NOT NULL,
	quantity INTEGER,
-- 	PRIMARY KEY(product_code, location_id)
    CONSTRAINT PK_Supplies PRIMARY KEY (product_code, location_id),
	FOREIGN KEY (product_code) REFERENCES Product (product_code) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (location_id) REFERENCES Store (location_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Houses (
	product_code INTEGER NOT NULL,
	location_id  INTEGER NOT NULL,
	quantity INTEGER,
	PRIMARY KEY (product_code, location_id),
	FOREIGN KEY (product_code) REFERENCES Product (product_code) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (location_id) REFERENCES Warehouse (location_id) ON DELETE CASCADE ON UPDATE CASCADE

);

CREATE TABLE Has(
	product_code INTEGER NOT NULL,
	package_id  INTEGER NOT NULL,
	quantity INTEGER,
	PRIMARY KEY(product_code, package_id),
	FOREIGN KEY (product_code) REFERENCES Product (product_code) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (package_id) REFERENCES Package (package_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Accesses (
	type_name CHAR(30),
	location_id INTEGER,
	PRIMARY KEY (type_name, location_id),
	FOREIGN KEY (location_id) REFERENCES Location_R4 (location_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Internal_Fleet (
	license_plate CHAR(7),
	status CHAR(16),
	company_name CHAR(30),
	PRIMARY KEY (license_plate),
	FOREIGN KEY(company_name) REFERENCES Company (company_name) ON DELETE SET NULL ON UPDATE CASCADE,
	FOREIGN KEY(license_plate) REFERENCES Transportation (license_plate) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE External_Company(
	company_name CHAR(30),
	contact_manager CHAR(20),
	business_start_date DATE,
	PRIMARY KEY (company_name)
);

CREATE TABLE External_Fleet (
	license_plate CHAR(7),
	contract_id INT,
	company_name CHAR(30),
	PRIMARY KEY (license_plate),
	FOREIGN KEY(company_name) REFERENCES External_Company (company_name) ON DELETE SET NULL ON UPDATE CASCADE,
	FOREIGN KEY(license_plate) REFERENCES Transportation (license_plate) ON DELETE CASCADE ON UPDATE CASCADE
);


CREATE TABLE Travels_to (
	license_plate CHAR(7),
	location_id INT,
	departure_DATE  DATE,
	arrival_DATE DATE,
	PRIMARY KEY (license_plate,location_id) 
);

-- get all each location's product quantity
CREATE VIEW Location_quantity AS
    SELECT Hs.location_id, Hs.product_code, Hs.quantity as quantity FROM Houses Hs
    UNION 
    (SELECT S.location_id, S.product_code, S.quantity as quantity FROM Supplies S
    UNION
    SELECT  H.package_id, H.product_code, H.quantity as quantity FROM Has H);

-- get each product quantity in the system
CREATE VIEW All_quantity(product_code, product_quantity) AS 
	SELECT product_code, SUM(quantity) FROM Location_quantity L GROUP BY L.product_code;


-- get the product quantity of each type of location
CREATE VIEW in_warehouse AS 
SELECT product_code, IFNULL(SUM(quantity), 0) AS quantity FROM Location_quantity LQ WHERE EXISTS (SELECT * FROM Houses Hs WHERE Hs.location_id = LQ.location_id) GROUP BY product_code;
CREATE VIEW in_store AS 
SELECT product_code, IFNULL(SUM(quantity), 0) AS quantity FROM Location_quantity LQ WHERE EXISTS (SELECT * FROM Supplies S WHERE S.location_id = LQ.location_id) GROUP BY product_code;
CREATE VIEW in_transit AS 
SELECT product_code, IFNULL(SUM(quantity), 0) AS quantity FROM Location_quantity LQ WHERE EXISTS (SELECT * FROM Package P WHERE P.package_id = LQ.location_id) GROUP BY product_code;




-- Insert data 
INSERT INTO Product
VALUES (1, 'Dried Mangoes', 'Mango Repub');
INSERT INTO Product
VALUES (2, 'Mango Juice', 'Mango Repub');
INSERT INTO Product
VALUES (3, 'Canned Peas', 'Canton Canning Company');
INSERT INTO Product
VALUES (4, 'Canned Tomatoes', 'Canton Canning Company');
INSERT INTO Product
VALUES (5, 'Eraser', 'Resare Limited');

INSERT INTO Location_R1
VALUES ('10800 170 Street, Surrey, BC', 604);
INSERT INTO Location_R1
VALUES ('57098 E Bakerview Road, Bellingham, WA',  360);
INSERT INTO Location_R1
VALUES ('6400 Macdonald Street, Vancouver, BC', 604);
INSERT INTO Location_R1
VALUES ('4900 Minoru Boulevard, Richmond, BC', 604);
INSERT INTO Location_R1
VALUES ('3600 28 Avenue, Delta, BC', 604);
INSERT INTO Location_R1
VALUES ('10900 180 Street, Surrey, BC', 604);
INSERT INTO Location_R1
VALUES ('2700 King Road, Abbotsford, BC', 604);
INSERT INTO Location_R1
VALUES ('5080 Pacific Street, Bellingham, WA', 360);
INSERT INTO Location_R1
VALUES ('9000 Steveston Highway, Richmond, BC', 604);
INSERT INTO Location_R1
VALUES ('300 Low level Rd, North Vancouver, BC', 604);

INSERT INTO Location_R3
VALUES ('18:00', '1:00', 7);
INSERT INTO Location_R3
VALUES ('19:00', '4:00', 9);
INSERT INTO Location_R3
VALUES ('18:00', '2:00', 8);
INSERT INTO Location_R3
VALUES ('6:00', '18:00', 12);
INSERT INTO Location_R3
VALUES ('4:00', '18:00', 14);

INSERT INTO Company 
VALUES ('AAA Canada');
INSERT INTO Company 
VALUES ('AAA US');
INSERT INTO Company 
VALUES ('AAA North America');
INSERT INTO Company 
VALUES ('AAA Global');
INSERT INTO Company 
VALUES ('AAA Quebec');

-- R4(location_id, address, location_name, phone_number, delivery_hours_start, delivery_hours_end, capacity, company_name)
INSERT INTO Location_R4
VALUES (101, '10800 170 Street, Surrey, BC', 'AAA Market Surrey', 5556666, '18:00', '1:00', 1000, 'AAA Canada');
INSERT INTO Location_R4
VALUES (102, '57098 E Bakerview Road, Bellingham, WA', 'AAA Market Bellingham', 7779999, '19:00', '4:00', 1500, 'AAA US');
INSERT INTO Location_R4
VALUES (103, '6400 Macdonald Street, Vancouver, BC', 'AAA Market Vancouver', 6663333, '18:00', '2:00', 2000, 'AAA Canada');
INSERT INTO Location_R4
VALUES (104, '4900 Minoru Boulevard, Richmond, BC', 'AAA Market Richmond', 6662222, '18:00', '2:00', 2000, 'AAA Canada');
INSERT INTO Location_R4
VALUES (105, '3600 28 Avenue, Delta, BC', 'AAA Market Delta', 6661111, '18:00', '1:00', 1000, 'AAA Canada');
INSERT INTO Location_R4
VALUES (223, '10900 180 Street, Surrey, BC', 'AAA Warehouse 23', 5557777, '6:00', '18:00', 15000, 'AAA North America');
INSERT INTO Location_R4
VALUES (226, '2700 King Road, Abbotsford, BC', 'AAA Warehouse 26', 5558888, '6:00', '18:00', 15000, 'AAA North America');
INSERT INTO Location_R4
VALUES (213, '5080 Pacific Street, Bellingham, WA', 'AAA Warehouse 13', 1112222, '6:00', '18:00', 15000, 'AAA North America');
INSERT INTO Location_R4
VALUES (215, '9000 Steveston Highway, Richmond, BC', 'AAA Warehouse 15', 5553333, '6:00', '18:00', 15000, 'AAA North America');
INSERT INTO Location_R4
VALUES (218, '300 Low level Rd, North Vancouver, BC', 'AAA Warehouse 18', 5552222, '4:00', '18:00', 7000, 'AAA North America'
);

INSERT INTO Store
VALUES (101, '10:00', '18:00');
INSERT INTO Store
VALUES (102, '10:00', '19:00');
INSERT INTO Store
VALUES (103, '10:00', '18:00');
INSERT INTO Store
VALUES (104, '10:00', '18:00');
INSERT INTO Store
VALUES (105, '10:00', '18:00');

INSERT INTO Warehouse
VALUES (223, 6);
INSERT INTO Warehouse
VALUES (226, 7);
INSERT INTO Warehouse
VALUES (213, 10);
INSERT INTO Warehouse
VALUES (215, 4);
INSERT INTO Warehouse
VALUES (218, 12);

INSERT INTO Vehicle_type
VALUES ('box_truck', 100);
INSERT INTO Vehicle_type
VALUES ('18_wheel', 500);
INSERT INTO Vehicle_type
VALUES ('pickup', 50);
INSERT INTO Vehicle_type
VALUES ('van', 75);
INSERT INTO Vehicle_type
VALUES ('flatbed', 200);

INSERT INTO Transportation
VALUES ('ABC 148', 'box_truck');
INSERT INTO Transportation
VALUES ('CDE 789', 'box_truck');
INSERT INTO Transportation
VALUES ('DEF 232', 'box_truck');
INSERT INTO Transportation
VALUES ('FGH 124', 'box_truck');
INSERT INTO Transportation
VALUES ('GHI 616', 'box_truck');
INSERT INTO Transportation
VALUES ('BCD 259', '18_wheel');
INSERT INTO Transportation
VALUES ('EFG 343', '18_wheel');
INSERT INTO Transportation
VALUES ('HIJ 120', '18_wheel');
INSERT INTO Transportation
VALUES('IJK 987', '18_wheel');
INSERT INTO Transportation
VALUES('JKL 676', '18_wheel');

INSERT INTO Package
VALUES (1101, 103, 226, NULL);
INSERT INTO Package
VALUES (1102, 104, 223, NULL);
INSERT INTO Package
VALUES (1103, 103, NULL, 'ABC 148');
INSERT INTO Package
VALUES (1104, 101, NULL, 'CDE 789');
INSERT INTO Package
VALUES (1105, 213, NULL, 'BCD 259');

INSERT INTO Supplies
VALUES (1, 101, 20);
INSERT INTO Supplies 
VALUES (1, 102, 20);
INSERT INTO Supplies
VALUES (1, 103, 12);
INSERT INTO Supplies
VALUES (2, 103, 2);
INSERT INTO Supplies 
VALUES (3, 103, 10);
INSERT INTO Supplies 
VALUES (4, 103, 15);
INSERT INTO Supplies
VALUES (1, 104, 16);
INSERT INTO Supplies
VALUES (3, 104, 8);
INSERT INTO Supplies
VALUES (4, 104, 15);
INSERT INTO Supplies
VALUES (4, 105, 12);
INSERT INTO Supplies 
VALUES (5, 105, 5);

INSERT INTO Houses
VALUES (1, 226, 50);
INSERT INTO Houses
VALUES (2, 226, 64);
INSERT INTO Houses
VALUES (1, 223, 81);
INSERT INTO Houses
VALUES (2, 213, 100);
INSERT INTO Houses
VALUES (1, 215, 10);

INSERT INTO Has
VALUES (1, 1101, 20);
INSERT INTO Has
VALUES (2, 1101, 12);
INSERT INTO Has
VALUES (1, 1102, 10);
INSERT INTO Has
VALUES (3, 1103, 20);
INSERT INTO Has
VALUES (1, 1103, 10);


INSERT INTO Internal_Fleet
VALUES ('ABC 148', 'delivery', 'AAA North America');
INSERT INTO Internal_Fleet
VALUES ('CDE 789', 'delivery', 'AAA North America');
INSERT INTO Internal_Fleet
VALUES ('DEF 232', 'maintenance', 'AAA North America');
INSERT INTO Internal_Fleet
VALUES ('FGH 124', 'maintenance', 'AAA North America');
INSERT INTO Internal_Fleet
VALUES ('GHI 616', 'standby', 'AAA North America');

INSERT INTO External_Company
VALUES ('FedUp', '6044440044', '2018-08-11');
INSERT INTO External_Company
VALUES ('LHD', '6045550055', '2018-08-19');
INSERT INTO External_Company
VALUES ('YYY Logistics', '6047770077', '2013-01-19');
INSERT INTO External_Company
VALUES ('RSE Global Transport', '6042220022', '2014-02-10');
INSERT INTO External_Company
VALUES ('Global Leap', '6041110011', '2020-02-29');

INSERT INTO External_Fleet
VALUES ('BCD 259', 000000, 'YYY Logistics');
INSERT INTO External_Fleet
VALUES ('EFG 343', 000001, 'YYY Logistics');
INSERT INTO External_Fleet
VALUES ('HIJ 120', 000002, 'YYY Logistics');
INSERT INTO External_Fleet
VALUES('IJK 987', 000002, 'RSE Global Transport');
INSERT INTO External_Fleet
VALUES('JKL 676', 000003, 'LHD');

INSERT INTO Accesses
VALUES ('box_truck', 101);
INSERT INTO Accesses
VALUES ('18_wheel', 101);
INSERT INTO Accesses
VALUES ('box_truck', 102);
INSERT INTO Accesses
VALUES ('box_truck', 103);
INSERT INTO Accesses
VALUES ('box_truck', 104);
INSERT INTO Accesses
VALUES ('box_truck', 105);
INSERT INTO Accesses
VALUES ('18_wheel', 105);

INSERT INTO Travels_to
VALUES ('ABC 148', 103, '2023-02-20 12:00:00', '2023-06-20 12:00:00');
INSERT INTO Travels_to
VALUES ('CDE 789', 101, '2023-02-20 12:00:00', '2023-06-20 12:00:00');
INSERT INTO Travels_to
VALUES ('GHI 616', 103, '2023-06-20 12:00:00', '2023-06-22 12:00:00');
INSERT INTO Travels_to
VALUES ('BCD 259', 213, '2023-02-24 12:00:00', '2023-05-20 12:00:00');
INSERT INTO Travels_to
VALUES('IJK 987', 215, '2023-05-10 12:00:00', '2023-05-12 12:00:00');
