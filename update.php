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

            <h3>Update Inventory Information</h3>

            <form action="update.php" method="get">
                <input type="radio" id="update_product" name="selected_action" value="update_product" checked="checked">
                <label for="update_product">Update Product Inventory Location</label>
                
                <br>
                
                <label for="product_id">Product ID:</label>
                <input type="number" id="product_id" name="product_id">

                <br>
                
                <label for="location_id">Location ID:</label>
                <input type="number" id="location_id" name="location_id">

                <br>

                <label for="quantity">New Quantity:</label>
                <input type="number" id="quantity" name="quantity">

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
                $product_id = $_GET['product_id'] ?? null;
                $location_id = $_GET['location_id'] ?? null;
                $quantity = $_GET['quantity'] ?? null;
            ?>

            <?php if ($selected_action == 'update_product' and $product_id != null and $location_id != null and $quantity != null): ?>

                <?php if ($selected_action == 'update_product'): ?>

                    <?php
                        #Existence/validity checks

                        #Check Product ID existence
                        include "scripts/exec_query.php";

                        $query = "SELECT * FROM Product P WHERE P.product_code = :pid";
                        $arguments = ['pid' => $product_id];

                        $data = exec_query($query, $arguments);

                        $pid_exists = (count($data) != 0);

                        #Check Location ID existence
                        $query = "SELECT * FROM Location_r4 LR4 WHERE LR4.location_id = :lid";
                        $arguments = ['lid' => $location_id];

                        $data = exec_query($query, $arguments);

                        $lid_exists = (count($data) != 0);

                        #Check Quantity validity

                        $quantity_valid = ($quantity >= 0);
                    ?>

                    <?php if ($pid_exists and $lid_exists and $quantity_valid): ?>

                        <?php 
                            #Determine if location_id refers to a store or a warehouse
                            $query = "SELECT * FROM Store S WHERE S.location_id = :lid";
                            $arguments = ['lid' => $location_id];

                            $data = exec_query($query, $arguments);

                            if (count($data) == 0) {
                                #is a warehouse
                                $location_type = 'warehouse';
                            } else {
                                $location_type = 'store';
                            }

                            #check whether we should INSERT or UPDATE
                            if ($location_type == 'store') {
                                $query = "SELECT S.quantity FROM Supplies S WHERE S.product_code = :pid AND S.location_id = :lid";
                                $arguments = ['pid' => $product_id, 'lid' => $location_id];

                                $data = exec_query($query, $arguments);
                            } else {
                                #warehouse
                                $query = "SELECT HS.quantity FROM Houses HS WHERE HS.product_code = :pid AND HS.location_id = :lid";
                                $arguments = ['pid' => $product_id, 'lid' => $location_id];

                                $data = exec_query($query, $arguments);
                            }

                            if (count($data) == 0) {
                                $action = 'insert';
                            } else {
                                $action = 'update';
                            }

                            $old_quantity = $data[0]['quantity'] ?? 0;
                        ?>
                    
                        <?php
                            ##### UPDATE #####

                            ### QUERY CONFIGURATION ###

                            #configure query from selected parameters
                            if ($location_type == 'store') {
                                if ($action == 'insert') {
                                    $query = "INSERT INTO Supplies VALUES ( :pid , :lid , :quantity );";
                                    $description = "Set quantity of product $product_id to $quantity at Store $location_id";
                                } else {
                                    $query = "UPDATE Supplies SET quantity = :quantity WHERE product_code = :pid AND location_id = :lid";
                                    $description = "Set quantity of product $product_id to $quantity from $old_quantity at Store $location_id";
                                }
                            } else {
                                #location is a warehouse
                                if ($action == 'insert') {
                                    $query = "INSERT INTO Houses VALUES ( :pid , :lid , :quantity );";
                                    $description = "Set quantity of product $product_id to $quantity at Warehouse $location_id";
                                } else {
                                    $query = "UPDATE Houses SET quantity = :quantity WHERE product_code = :pid AND location_id = :lid";
                                    $description = "Set quantity of product $product_id to $quantity from $old_quantity at Warehouse $location_id";
                                }
                            }
                            $arguments = ['pid' => $product_id, 'lid' => $location_id, 'quantity' => $quantity];

                            ### QUERY EXECUTION ###

                            $data = exec_query($query, $arguments);

                            ##### RETRIEVAL #####

                            ### QUERY CONFIGURATION ###

                            #configure query from selected parameters
                            if ($location_type == 'store') {
                                $query = "SELECT * FROM Supplies S WHERE S.product_code = :pid AND S.location_id = :lid";
                                $arguments = ['pid' => $product_id, 'lid' => $location_id];
                            } else {
                                #warehouse
                                $query = "SELECT * FROM Houses HS WHERE HS.product_code = :pid AND HS.location_id = :lid";
                                $arguments = ['pid' => $product_id, 'lid' => $location_id];
                            }

                            ### QUERY EXECUTION ###

                            $data = exec_query($query, $arguments);
                            
                            ### DATA DISPLAY ###

                            include "scripts/display_data.php";

                            #display description
                            echo("<h3>$description</h3>\n");

                            #display table of results
                            echo construct_table($data, ['product_code', 'location_id', 'quantity'], ["Product Code", "Location ID", "Quantity"]);

                            if (count($data) == 0) {
                                echo "Update Failed";
                            }

                        ?>

                    <?php else: ?>

                        <?php echo "<h3>Invalid Input - Product ID or Location ID does not exist, or new quantity is invalid</h3>\n"; ?>

                    <?php endif ?>

                <?php else: ?>

                <?php endif; ?>

            <?php else: ?>

                <?php echo "<h3>Invalid Input</h3>\n"; ?>

            <?php endif; ?>

        </div>

        <?php include "html/footer.html"; ?>

    </body>
</html>
