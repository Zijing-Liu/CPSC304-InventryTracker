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

            <h3>Check product quantity with product code</h3>

            <form action="product_overview.php" method="get">
                <label for="product_code"> Product code: </label>
                <input type="number" id="product_code" name="product_code">

                <br><br>

                <input type="submit">

            </form>

        </div>

        <hr>


        <div class="output">

            <h2>Product_overview</h2>

            <?php 
                ### PARAMETERS ###

                #parameters from coalescing superglobal
                $product_code = $_GET['product_code'] ?? 1000;
            ?>

            <?php if ($product_code != 1000): ?>

            <?php

                ### QUERY CONFIGURATION ###

                $query = "CREATE VIEW [Temp] AS
                            SELECT sum(quantity) FROM Houses WHERE product_code = $product_code
                            UNION
                            (SELECT sum(quantity) FROM Supplies WHERE product_code =$product_code
                            UNION
                            SELECT sum(quantity) FROM HAS WHERE product_code = $product_code);
            
                            SELECT Pd.product_code, Pd.product_name, Pd.company_name, SUM(T.quantity) as total_qantity
                            FROM Temp T, Product Pd
                            WHERE Pd.product_code = $product_code";
                            
                $arguments = null;
                $description = "product's total quantity in the inventory system";

                ### QUERY EXECUTION ###

                include "scripts/exec_query.php";
                $data = exec_query($query, $arguments);
                
                ### DATA DISPLAY ###

                include "scripts/display_data.php";

                #display description
                echo("<h3>$description</h3>\n");

                #display table of results
                echo construct_table($data, ['product_code', 'product_name', 'company_name','total_qantity'],
                                            ["Product Code", "Name", "Manufacturer",  "Total Quantity"]);

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
