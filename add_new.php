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

            <h3>Add/Delete Products</h3>

            <form action="add_new.php" method="get">
                <input type="radio" id="add_product" name="selected_action" value="add_product" checked="checked">
                <label for="add_product">Add a new Product</label>
                
                <br>
                
                <label for="add_product_id">Product ID:</label>
                <input type="number" id="add_product_id" name="add_product_id">

                <br>

                <label for="product_name">Product Name:</label>
                <input type="text" id="product_name" name="product_name">

                <br>

                <label for="product_manufacturer">Manufacturer:</label>
                <input type="text" id="product_manufacturer" name="product_manufacturer">

                <br><br>

                <input type="radio" id="rem_product" name="selected_action" value="rem_product">
                <label for="rem_product">Delete a Product</label>
                
                <br>
                
                <label for="rem_product_id">Product ID:</label>
                <input type="number" id="rem_product_id" name="rem_product_id">

                <br><br>

                <!-- <input type="radio" id="add_location" name="selected_action" value="location">
                <label for="add_location">Add a new Location</label>
                
                <br>

                <label>Location Type:</label>
                <input type="radio" id="type_store" name="selected_loctype" value="store">
                <label for="type_store">Store</label>
                <input type="radio" id="type_warehouse" name="selected_loctype" value="warehouse">
                <label for="type_warehouse">Warehouse</label>

                <br>

                <label for="location_id">New Location ID:</label>
                <input type="number" id="location_id" name="location_id">

                <br>

                <br><br> -->

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
                $add_product_id = $_GET['add_product_id'] ?? null;
                $rem_product_id = $_GET['rem_product_id'] ?? null;
                $product_name = $_GET['product_name'] ?? null;
                $product_manufacturer = $_GET['product_manufacturer'] ?? null;
            ?>

            <?php if ($selected_action == 'add_product' and $add_product_id != null and $product_name != null and $product_manufacturer != null
                    or $selected_action == 'rem_product' and $rem_product_id != null): ?>

                <?php if ($selected_action == 'add_product'): ?>

                    <?php
                        $product_id = $add_product_id;

                        #Check Product ID uniqueness
                        include "scripts/exec_query.php";

                        $query = "SELECT * FROM Product P WHERE P.product_code = :pid";
                        $arguments = ['pid' => $product_id];

                        $data = exec_query($query, $arguments);
                    ?>

                    <?php if (count($data) == 0): ?>
                    
                        <?php
                            ##### INSERTION #####

                            ### QUERY CONFIGURATION ###

                            #configure query from selected parameters
                            $query = "INSERT INTO Product VALUES ( :p_code , :p_name , :p_manu );";
                            $arguments = ['p_code' => $product_id, 'p_name' => $product_name, 'p_manu' => $product_manufacturer];
                            $description = "Inserted new product into system";

                            ### QUERY EXECUTION ###

                            $data = exec_query($query, $arguments);

                            ##### RETRIEVAL #####

                            ### QUERY CONFIGURATION ###

                            #configure query from selected parameters
                            $query = "SELECT * FROM Product P WHERE P.product_code = :pid";
                            $arguments = ['pid' => $product_id];

                            ### QUERY EXECUTION ###

                            $data = exec_query($query, $arguments);
                            
                            ### DATA DISPLAY ###

                            include "scripts/display_data.php";

                            #display description
                            echo("<h3>$description</h3>\n");

                            #display table of results
                            echo construct_table($data, ['product_name', 'product_code', 'company_name'], ["Name", "Product Code", "Manufacturer"]);

                            if (count($data) == 0) {
                                echo "Insertion Failed";
                            }

                        ?>

                    <?php else: ?>

                        <?php echo "<h3>Product with code $product_id already exists</h3>\n"; ?>

                    <?php endif ?>

                <?php elseif ($selected_action == 'rem_product'): ?>

                    <?php
                        $product_id = $rem_product_id;
                        
                        #Check Product ID existence
                        include "scripts/exec_query.php";

                        $query = "SELECT * FROM Product P WHERE P.product_code = :pid";
                        $arguments = ['pid' => $product_id];

                        $data = exec_query($query, $arguments);
                    ?>

                    <?php if (count($data) == 1): ?>
                    
                        <?php
                            ##### DELETION #####

                            ### QUERY CONFIGURATION ###

                            #configure query from selected parameters
                            $query = "DELETE FROM Product P WHERE P.product_code = :pid";
                            $arguments = ['pid' => $product_id];
                            $description = "Deleted product $product_id from system";

                            ### QUERY EXECUTION ###

                            $data = exec_query($query, $arguments);

                            ##### RETRIEVAL #####

                            ### QUERY CONFIGURATION ###

                            #configure query from selected parameters
                            $query = "SELECT * FROM Product P WHERE P.product_code = :pid";
                            $arguments = ['pid' => $product_id];

                            ### QUERY EXECUTION ###

                            $data = exec_query($query, $arguments);
                            
                            ### DATA DISPLAY ###

                            include "scripts/display_data.php";

                            #display description
                            echo("<h3>$description</h3>\n");

                            if (count($data) == 1) {
                                echo "Deletion Failed";
                            }

                        ?>

                    <?php else: ?>

                        <?php echo "<h3>Product with code $product_id does not exist</h3>\n"; ?>

                    <?php endif ?>

                <?php endif; ?>

            <?php else: ?>

                <?php echo "<h3>Invalid Input</h3>\n"; ?>

            <?php endif; ?>

        </div>

        <?php include "html/footer.html"; ?>

    </body>
</html>
