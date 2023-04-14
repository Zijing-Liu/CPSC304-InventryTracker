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

            <h3>Template</h3>

            <form action="template.php" method="get">
                <label for="example">Example: </label>
                <input type="text" id="example" name="example">

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
                $example = $_GET['example'] ?? null;

            ?>

            <?php if ($example != null): ?>

            <?php

                ### QUERY CONFIGURATION ###

                $query = "SELECT * FROM product P";
                $arguments = null;
                $description = "Example: all products";

                ### QUERY EXECUTION ###

                include "scripts/exec_query.php";
                $data = exec_query($query, $arguments);
                
                ### DATA DISPLAY ###

                include "scripts/display_data.php";

                #display description
                echo("<h3>$description</h3>\n");

                #display table of results
                echo construct_table($data, ['product_name', 'product_code', 'company_name'], ["Name", "Product Code", "Manufacturer"]);

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
