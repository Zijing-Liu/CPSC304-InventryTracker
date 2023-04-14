<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
        <?php include "html/headernav.html"; ?>

        <div class="userinput">

            <h2>Deleting package info</h2>

            <h3>T</h3>




            <form action= "Del_Packages.php" method="get">
                <label for="package_id">Enter delivered package id</label>
                <input type="text" id="package_id" name="package_id">
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
                $package_id = $_GET['package_id'] ?? null;
		
               

            ?>

            <?php if ($package_id != null): ?>
            

            <?php

                ### QUERY CONFIGURATION ###

                $query = "DELETE FROM Package WHERE package_id = $package_id";

                $arguments = null;
                $description = "Deleting Packages";

                ### QUERY EXECUTION ###

                include "scripts/exec_query.php";
                
                ### DATA DISPLAY ###

                include "scripts/display_data.php";

                #display description
                echo("<h3>$description</h3>\n");

                #display table of results
                #echo construct_table($data, ['lpackage_id'], ["Package ID"]);

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