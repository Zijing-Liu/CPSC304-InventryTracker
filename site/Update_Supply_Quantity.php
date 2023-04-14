<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
        <?php include "html/headernav.html"; ?>

        <div class="userinput">

            <h2>Updating Quantity after delivering packages</h2>

            <h3>T</h3>


<!-- /* UPDATE Supplies s
SET s.quantity = s.quantity - 5
WHERE s.product_code = 1 AND s.location_id = 104;*/ -->



            <form action="Update_Supply_Quantity.php" method="get">

                <label for="product_code">Enter product code</label>
                <input type="text" id="product_code" name="product_code">
                <br>
                
               <label for="location_id">Enter location id</label>
                <input type="text" id="location_id" name="location_id">
                <br>

		<label for="quantity">Enter delivered quantity</label>
                <input type="text" id="quantity" name="quantity">
                <br>

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

                $product_code = $_GET['product_code'] ?? null;
		        $location_id = $_GET['location_id'] ?? null;
		        $quantity= $_GET['quantity'] ?? null;
		
               

            ?>

            <?php if ($product_code != null): ?>
            

            <?php

                ### QUERY CONFIGURATION ###

                $query = "UPDATE Supplies s
			    SET s.quantity = s.quantity - $quantity
			    WHERE s.product_code =  $product_code AND s.location_id = $location_id";

                $arguments = null;
                $description = "Updated Supplies Quantity";

                ### QUERY EXECUTION ###

                include "scripts/exec_query.php";
                $data = exec_query($query, $arguments);
                
                ### DATA DISPLAY ###

                include "scripts/display_data.php";

                #display description
                echo("<h3>$description</h3>\n");

                #display table of results
                #echo construct_table($data, ['lpackage_id’], ["Package ID"]);

                #if (count($data) == 0) {
                    #echo "No results";
                #}

            ?>

            <?php else: ?>

            <?php echo "<h3>No results</h3>\n"; ?>

            <?php endif ?>

        </div>

        <?php include "html/footer.html"; ?>

    </body>
</html> 