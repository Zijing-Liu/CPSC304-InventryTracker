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

        <h3>Enter a set Product IDs to check stores that have all of them</h3>


<!-- /*SELECT s.location_id, s.product_code,COUNT(DISTINCT s.product_code)
FROM Supplies s
WHERE s.product_code IN (1,2,3)
GROUP BY s.location_id
HAVING COUNT(DISTINCT s.product_code) = 2;*/ -->



            <form action="Supplies_count.php" method="get">
                <label for="product_code">Enter an array of product IDs, separated by commas: </label>
                <input type="text" id="product-code" name="product_code">

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

                if($product_code != null) {
                    $product_code_arr = $integerIDs = array_map('intval', explode(',', $product_code));
                    $product_code_count = count($product_code_arr);
                } else {
                    $product_code_arr = [];
                    $product_code_count = 0;
                }

            ?>

            <?php if ($product_code_count > 0): ?>
            

            <?php

                ### QUERY CONFIGURATION ###

                $query = "SELECT s.location_id
			    FROM Supplies s
			    WHERE s.product_code IN ( $product_code )
			    GROUP BY s.location_id
			    HAVING COUNT(DISTINCT s.product_code) = $product_code_count; ";
                $arguments = null;
                $description = "Stores Supplying All Products in List:";

                ### QUERY EXECUTION ###

                include "scripts/exec_query.php";
                $data = exec_query($query, $arguments);
                
                ### DATA DISPLAY ###

                include "scripts/display_data.php";

                #display description
                echo("<h3>$description</h3>\n");

                #display table of results
                echo construct_table($data, ['location_id'], ["Location ID"]);

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
