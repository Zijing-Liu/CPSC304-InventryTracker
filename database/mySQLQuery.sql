
-- get the the location a package is delivered to
SELECT P.package_id, LR4.location_id, LR4.address
FROM location_R4 LR4, Package P
WHERE LR4. location_id = P.location_id


-- get the carrier information of a package if it's in transit
SELECT P.license_plate, P.destination, Tr.departure_date, Tr.arrival_DATE
FROM Package P, Travels_to Tr
WHERE P.license_plate = Tr.license_plate


--get package_id and it's current location if is was dilvered to a wrong place
SELECT P.package_id, LR4.location_id, LR4.address
FROM location_R4 LR4, Package P
WHERE P.location_id <> P.destination and LR4.location_id = P.location_id 


--check the quantity of a product in stock of each warehouse
SELECT H.location_id, H.product_code, COUNT(Pd.product_code) as quantity
FROM Houses H, Product Pd, Location_R4 LR4
WHERE H.product_code = Pd.product_code and H.location_id = LR4.location_id
GROUP BY H.location_id, H.product_code

SELECT sum(quantity) FROM Houses WHERE product_code = 1
UNION
(SELECT sum(quantity) FROM Supplies WHERE product_code = 1
UNION
SELECT sum(quantity) FROM HAS WHERE product_code = 1)

SELECT Pd.product_code, Pd.product_name, Pd.company_name, SUM(IFNULL(Hs.quantity, 0) + IFNULL(S.quantity, 0) + IFNULL(H.quantity, 0)) as total_qantity
                FROM HOUSES Hs, Supplies S, Has H, Product Pd
                WHERE Hs.product_code = $product_code and H.product_code = $product_code and S.product_code = $product_code and Pd.product_code = $product_code;

-- Package location & contents - but if it’s on Transportation, current trip details
SELECT Pk.package_id, Pd.product_name, H.quantity, LR4.address, concat(LR1.area_code, LR4.phone_number) as phone
FROM Package Pk, Location_R4 LR4, Location_R1 LR1, Has H, Product Pd
WHERE Pk.location_id = LR4.location_id and LR4.address = LR1.address 
      and Pk.package_id = H.package_id and H.product_code = Pd.product_code;

-- Return the trip details of a package if it's in transportation
SELECT Pk.package_id, Pd.product_name, H.quantity, Tr.license_plate, Tr. departure_date, Tr.arrival_DATE
FROM Package Pk, Has H, Product Pd, Transportation T, Travels_to Tr
WHERE Pk.license_plate = T.license_plate and T.license_plate = Tr.license_plate
      and Pk.package_id = H.package_id and H.product_code = Pd.product_code;

-- For each product, quantities throughout the distribution network (at each store, warehouse, in transit) 
SELECT Hs.product_code, Pd.product_name, (Hs.quantity + S.quantity + H.quantity) as total_qantity
FROM HOUSES Hs, Supplies S, Has H, Product Pd
WHERE Hs.product_code = H.product_code = S.product_code = Pd.product_name
GROUP BY Hs.product_code;

-- For each Location, the quantity of each product that it has (filter by specific products)
SELECT LR4.location_id, Pd.product_code, pd.product_name, Hs.quantity
FROM Houses HS, Product Pd, Location_R4 LR4
WHERE LR4.location_id = HS.location_id and LR4.location_id = HS.location_id and Hs.product_code = pd.product_code
GROUP BY pd.product_code
UNION
SELECT LR4.location_id, Pd.product_code, Pd.product_name, S.quantity
FROM Supplies S, Product Pd, Location_R4 LR4
WHERE LR4.location_id = S.location_id and LR4.location_id = S.location_id and S.product_code = pd.product_code
GROUP BY pd.product_code;

-- Search Products by name (with a keyword) or ID 
SELECT product_code, product_name, company_name
FROM Product 
WHERE product_name like '%keyword%' or product_code = 'num';

-- Specify a company and see which products it makes
SELECT product_code, product_name
FROM Product
WHERE company_name LIKE '%keyword%';

--For a given vehicle and store, see if the vehicle can access that store
For a given vehicle and store, see if the vehicle can access that store
SELECT *
FROM Accesses a
WHERE a.type_name = <vehicle_type_name> AND a.location_id = <store_location_id>;


--For a set of products, the Locations that have all of them (Division)
SELECT s.location_id
FROM Supplies s
WHERE s.product_code IN (<product_code_1>, <product_code_2>, ..., <product_code_n>)
GROUP BY s.location_id
HAVING COUNT(DISTINCT s.product_code) = <number_of_products>;

--For a package that is delivered, delect the package
DELETE FROM Package
WHERE package_id = <package_id>;

--Update the quantity of the quantity of the product when the package is delivered 
UPDATE Supplies s
SET s.quantity = s.quantity - <delivered_quantity>
WHERE s.product_code = <product_code> AND s.location_id = <delivery_location_id>;



-- Nested Aggregation with Group By: Find the store that has the most of the item that is in the shortest supply 
-- (important to implement query that can sum up the quantity of a specific product across all Locations and Packages in the system)

SELECT S.location_id, S.product_code, S.quantity
FROM Supplies S
WHERE S.product_code IN (SELECT AQ.product_code FROM All_quantity as AQ
				WHERE AQ.product_quantity <= all (SELECT AQ1.product_quantity FROM All_quantity as AQ1))
      AND S.quantity <= all (SELECT S2.quantity FROM Supplies S2 WHERE S.product_code = S2.product_code);
      