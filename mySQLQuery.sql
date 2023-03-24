-- check out the total list of product 
DESCRIBE PRODUCT

-- insert a new product into the Product table
-- display all the products
<?php echo $product['product_name']>


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