<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
        <?php include "html/headernav.html"; ?>

        <div class="userinput">

        <h2>Action Selection & Input</h2>

        <h3>Enter desired Vehicle Type and Location ID to check against</h3>

            <form action="store_access.php" method="get">
                <label for="vehicle_type">Vehicle Type: </label>
                <input type="text" id="vehicle_type" name="vehicle_type">
                <br>
                <label for="location_id"> Location ID: </label>
                <input type="text" id="location_id" name="location_id">

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
                $vehicle_type = $_GET['vehicle_type'] ?? null;
                $location_id = $_GET['location_id'] ?? null;

            ?>

            <?php if ($vehicle_type != null and $location_id != null): ?>

            <?php

                ### QUERY CONFIGURATION ###

                $query = "SELECT a.type_name,l.location_name,l.location_id
                          FROM Accesses a, Location_R4 l
                          WHERE  a.location_id=l.location_id AND a.type_name = \"$vehicle_type\" AND a.location_id =$location_id ";
                $arguments = null;
                $description = "Store access description";

                ### QUERY EXECUTION ###

                include "scripts/exec_query.php";
                $data = exec_query($query, $arguments);
                
                ### DATA DISPLAY ###

                include "scripts/display_data.php";

                #display description
                echo("<h3>$description</h3>\n");

                #display table of results
                echo construct_table($data, ['type_name', 'location_name', 'location_id'], ["Vehicle Type", "Location Name", "Location ID"]);

                if (count($data) == 0) {
                    echo "No results";
                }

            ?>

            <?php else: ?>

            <?php echo "<h3>No results</h3>\n"; ?>

            <?php endif ?>

        </div>

        <?php include "html/footer.html"; ?>

    </body>
</html> 
