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

            <h3>Stock Information</h3>

            <form action="stock.php" method="get">
                <input type="radio" id="act_stock_location" name="selected_action" value="stock_location" checked="checked">
                <label for="act_stock_location">View Inventory by Location</label><br>
                
                <label for="location_id">Location ID:</label>
                <input type="number" id="location_id" name="location_id">

                <br><br>

                <input type="radio" id="act_stock_product" name="selected_action" value="stock_product">
                <label for="act_stock_product">View Inventory by Product</label><br>

                <label for="product_id">Product ID:</label>
                <input type="number" id="product_id" name="product_id">

                <br><br>

                <label for="threshold">Threshold:</label>
                <input type="number" id="threshold" name="threshold">

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
                $selected_action = $_GET['selected_action'] ?? null;
                $location_id = $_GET['location_id'] ?? null;
                $product_id = $_GET['product_id'] ?? null;
                $threshold = $_GET['threshold'] ?? null;

                ### QUERY CONFIGURATION ###

                #configure query from selected parameters
                $sql_base1 = "SELECT S.location_id, S.product_code, S.quantity, Pd.product_name
                FROM supplies S, product Pd, location_r4 LR4
                WHERE S.product_code = Pd.product_code AND S.location_id = LR4.location_id ";

                $sql_base2 = "GROUP BY S.location_id, S.product_code\n";

                $query = $sql_base1;
                $arguments = [];

                if ($selected_action == "stock_location" and $location_id != null) {
                    $query .= " AND S.location_id = :location_id\n";
                    $arguments['location_id'] = $location_id;
                    $description = "Showing inventory at location $location_id";
                } else if ($selected_action == "stock_product" and $product_id != null) {
                    $query .= " AND S.product_code = :product_id\n";
                    $arguments['product_id'] = $product_id;
                    $description = "Showing inventory of product $product_id";
                } else {
                    $description = "Showing inventory information";
                }

                $query = $query.$sql_base2;

                if ($threshold != null) {
                    $query .= "HAVING S.quantity < :threshold";
                    $arguments['threshold'] = $threshold;
                    $description .= " where less than $threshold units";
                }

                #echo $query."<br><hr>"; #DEBUG

                ### QUERY EXECUTION ###

                include "scripts/exec_query.php";
                $data = exec_query($query, $arguments);
                
                ### DATA DISPLAY ###

                include "scripts/display_data.php";

                #display description
                echo("<h3>$description</h3>\n");

                #display table of results
                if ($selected_action == "stock_location" and $location_id != null) {
                    echo construct_table($data, ['product_code', 'product_name', 'quantity'], ["Product Code", "Product Name", "Quantity"]);
                } else if ($selected_action == "stock_product" and $product_id != null) {
                    echo construct_table($data, ['location_id', 'quantity'], ["Location ID", "Quantity"]);
                } else {
                    echo construct_table($data, ['location_id', 'product_code', 'product_name', 'quantity'], ["Location ID", "Product Code", "Name", "Quantity"]);
                }

                ?>

        </div>

        <?php include "html/footer.html"; ?>

    </body>
</html>
