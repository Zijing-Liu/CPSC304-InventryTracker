<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
        <?php include "html/headernav.html"; ?>

        <div class="output">

            <h2>Output</h2>

            <?php

                ### QUERY CONFIGURATION ###
                $query = "SELECT S.location_id, S.product_code, S.quantity
                              FROM Supplies S
                                   WHERE S.product_code IN (SELECT AQ.product_code FROM All_quantity as AQ
					                                        WHERE AQ.product_quantity <= all (SELECT AQ1.product_quantity FROM All_quantity as AQ1))
                                         AND S.quantity <= all (SELECT S2.quantity FROM Supplies S2 WHERE S.product_code = S2.product_code)";             
                              
                $arguments = null;
                $description = "";

                ### QUERY EXECUTION ###
                include "scripts/exec_query.php";
                $data = exec_query($query, $arguments);

                ### DATA DISPLAY ###

                include "scripts/display_data.php";

                $location_id = $data[0]['location_id'];
                $product_code = $data[0]['product_code'];
                $quantity = $data[0]['quantity'];

                #display description
                echo("<h3>Store $location_id has stocked the most ($quantity units) of product $product_code, which is in shortest supply across the system</h3>\n");

                #display table of results
                echo construct_table($data, ['location_id', 'product_code', 'quantity'], ["Store Id", "product code", "quantity"]);
            
                if (count($data) == 0) {
                    echo "No results";
                }

            ?>


        </div>

        <?php include "html/footer.html"; ?>

    </body>
</html>
