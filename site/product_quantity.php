<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
        <?php include "html/headernav.html"; ?>
        <?php ##include "./product_quantity_table.php"; ?>


        <div class="userinput">

            <h2>Action Selection & Input</h2>

            <h3>Check product quantity with product code</h3>

            <form action="product_quantity.php" method="get">
                <label for="product_code"> Product code: </label>
                <input type="number" id="product_code" name="product_code">

                <input type="submit">

            </form>
            <br><br>


        </div>

        <hr>

        <div class="output">

            <h2>Search Results</h2>

            <?php 
                ### PARAMETERS ###

                #parameters from coalescing superglobal
                $product_code = $_GET['product_code'] ?? 1000;
            ?>

            <?php if ($product_code != 1000): ?>    

            <?php

                ### QUERY CONFIGURATION ###

                $query = "SELECT al.product_code, IFNULL(w.quantity, 0) as w_quantity, IFNULL(s.quantity, 0) as s_quantity, IFNULL(t.quantity, 0) as t_quantity, IFNULL(al.product_quantity, 0) as total_quantity 
                FROM (all_quantity al
                LEFT JOIN in_warehouse w
                on al.product_code = w.product_code
                LEFT JOIN in_store s
                ON al.product_code = s.product_code 
                LEFT JOIN in_transit t
                ON al.product_code = t.product_code)
                WHERE al.product_code = $product_code";
                
                $arguments = null;
                $description = "Product quantity in the inventory systems";

                ### QUERY EXECUTION ### 

                include "scripts/exec_query.php";
                $data = exec_query($query, $arguments);
                
                ### DATA DISPLAY ###

                include "scripts/display_data.php";

                #display description
                echo("<h3>$description</h3>\n");

                #display table of results
                echo construct_table($data, [ 'product_code', 'w_quantity', 's_quantity', 't_quantity', 'total_quantity'], ["Product Code", "In Warehouses","In Stores", "In Transit", "Total quantity"]);

                if (count($data) == 0) {
                    echo "No results";
                }

                echo("<h3>Product Quantity Overview</h3>");

                ### QUERY CONFIGURATION ###

                $query = "SELECT * From location_quantity LQ WHERE LQ.product_code = :pid";// WHERE product_code = :pid";
                $arguments = ['pid' => $product_code];

                ### QUERY EXECUTION ### 

                $data = exec_query($query, $arguments);
                
                ### DATA DISPLAY ###

                #display table of results
                echo construct_table($data, ['location_id', 'product_code', 'quantity'], ["Location ID", "Product Code", "Quantity"]);
         
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
