<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
        <?php include "html/headernav.html"; ?>
  
        <div class="userinput" >

            <h2>Action Selection & Input</h2>

            <h3>Track pacakge by ID</h3>

            <form action="package_track.php" method="get">
                <label for="package_id">Package ID </label>
                <input type="number" id="package_id" name="package_id">

                <br><br>

                <input type="submit">

            </form>

        </div>
        
        <hr>

        <div class="output">

            <h2>Output</h2>

            <?php 
                ### PARAMETERS ###

                #parameters from coalescing superglobal
                $package_id = $_GET['package_id'] ?? 1000;         
            ?>


            <?php 
                include "./scripts/exec_query.php";
                
                $query = "SELECT ifnull(location_id, 0) as id FROM Package WHERE package_id = $package_id";
                $arguments = null;
                $data = exec_query($query, $arguments);
                if (count($data) != 0) {
                    $row = $data[0];
                    $location_id = $row['id'];
                }
                ?>

            <?php if (count($data) != 0): ?>

            <?php
                ### QUERY CONFIGURATION ###
                
                if($location_id != 0 ){
                    $query = "SELECT Pk.package_id, Pd.product_name, H.quantity, Pk.destination, LR4.address, concat(LR1.area_code, LR4.phone_number) as phone
                    FROM Package Pk, Location_R4 LR4, Location_R1 LR1, Has H, Product Pd
                    WHERE Pk.location_id = LR4.location_id and LR4.address = LR1.address 
                          and Pk.package_id = $package_id and H.package_id = $package_id and H.product_code = Pd.product_code";
                    $arguments = null;
                    $description = "Status of the Package $package_id";
                } else{
                    $query = "SELECT Pk.package_id, Pd.product_name, H.quantity, Pk.destination, Tr.license_plate, Tr.departure_date, Tr.arrival_DATE
                    FROM Package Pk, Has H, Product Pd, Transportation T, Travels_to Tr
                    WHERE Pk.license_plate = T.license_plate and T.license_plate = Tr.license_plate and Pk.package_id = $package_id and $package_id = H.package_id and H.product_code = Pd.product_code";
                    $arguments = null;
                    $description = "The package $package_id is in transit, here is the trip information";
                }
                    

                ### QUERY EXECUTION ###

                $data = exec_query($query, $arguments);
                
                ### DATA DISPLAY ###

                include "./scripts/display_data.php";

                #display description
                echo("<h3>$description</h3>\n");

                #display table of results
                if($location_id != 0) {
                    echo construct_table($data, ['package_id', 'product_name', 'quantity' ,'destination', 'address', 'phone'], ["Package ID", "Product Contained", "Quantity", "Shipping To", "Current Location", "Contact Location"]);
                } else {
                    echo construct_table($data,['package_id', 'product_name', 'quantity', 'destination', 'license_plate', 'departure_date'], 
                                                ["Package ID", "Product Contained", "Quantity", "Shipping To", "License Plate", "Departure Date"]);
                }
                
                
                if (count($data) == 0) {
                    echo "No results, package empty";
                }

            ?>

            <?php else: ?>

            <?php echo "<h3>No results</h3>\n"; ?>

            <?php endif ?>

        </div>

        <?php include "html/footer.html"; ?>

    </body>
</html>
