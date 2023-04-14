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

            <h3>Update Product Information</h3>

            <form action="update_product.php" method="get">
                <label for="product_id">Select Product ID to edit:</label>
                <input type="number" id="product_id" name="product_id">

                <br>

                <p>(Leave fields blank to retain the old values.)</p>
                
                <label for="new_pname">New Product Name:</label>
                <input type="text" id="new_pname" name="new_pname">

                <br>

                <label for="new_pmanu">New Manufacturer:</label>
                <input type="text" id="new_pmanu" name="new_pmanu">

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
                $product_id = $_GET['product_id'] ?? null;
                $new_pname = $_GET['new_pname'] ?? null;
                $new_pmanu = $_GET['new_pmanu'] ?? null;
            ?>

            <?php if ($product_id != null): ?>

                <?php
                    #Existence/validity checks

                    #Check Product ID existence
                    include "scripts/exec_query.php";

                    $query = "SELECT * FROM Product P WHERE P.product_code = :pid";
                    $arguments = ['pid' => $product_id];

                    $data = exec_query($query, $arguments);

                    $pid_exists = (count($data) != 0);

                    #Check validity of other arguments
                    $new_valid = !(($new_pname == '' or $new_pname == null) and ($new_pname == '' or $new_pmanu == null));
                ?>

                <?php if ($pid_exists and $new_valid): ?>
                
                    <?php
                        $old_pdata = $data;

                        ##### UPDATE #####

                        ### QUERY CONFIGURATION ###

                        #configure query from selected parameters
                        if ($new_pname == null or $new_pname == '') {
                            $new_pname = $old_pdata[0]['product_name'];
                        }
                        if ($new_pmanu == null or $new_pmanu == '') {
                            $new_pmanu = $old_pdata[0]['company_name'];
                        }

                        $query = "UPDATE product SET product_name = :new_pname , company_name = :new_pmanu WHERE product_code = :pid";
                        $arguments = ['pid' => $product_id, 'new_pname' => $new_pname, 'new_pmanu' => $new_pmanu];
                        $description = "Adjusted values for product $product_id";

                        ### QUERY EXECUTION ###

                        $data = exec_query($query, $arguments);

                        ##### RETRIEVAL #####

                        ### QUERY CONFIGURATION ###

                        #configure query from selected parameters
                        $query = "SELECT * FROM Product P WHERE P.product_code = :pid";
                        $arguments = ['pid' => $product_id];

                        ### QUERY EXECUTION ###

                        $new_pdata = exec_query($query, $arguments);
                        
                        ### DATA DISPLAY ###

                        include "scripts/display_data.php";

                        #display description
                        echo("<h3>$description</h3>\n");

                        #display old data
                        echo("<p>From old values:</p>\n");
                        echo construct_table($old_pdata, ['product_code', 'product_name', 'company_name'], ["Product Code", "Name", "Manufacturer"]);

                        #display new data
                        echo("<p>To new values:</p>\n");
                        echo construct_table($new_pdata, ['product_code', 'product_name', 'company_name'], ["Product Code", "Name", "Manufacturer"]);
                    ?>

                <?php else: ?>

                    <?php 
                        if (!$pid_exists) {
                            echo "<h3>Invalid Input - Product ID does not exist</h3>\n"; 
                        }

                        if (!$new_valid) {
                            echo "<h3>Invalid Input - new field values are empty, or no change</h3>\n";
                        }
                    ?>

                <?php endif ?>

            <?php else: ?>

                <?php echo "<h3>Invalid Input - Product ID required</h3>\n"; ?>

            <?php endif; ?>

        </div>

        <?php include "html/footer.html"; ?>

    </body>
</html>
