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

            <h3>Search Product by Company</h3>

            <form action="product_search.php" method="get">
                <label for="search_term">Search term:</label>
                <input type="text" id="search_term" name="search_term">

                <br><br>

                <label for="limit">Number of results to display:</label>
                <input type="number" id="limit" name="limit">

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
                $search_term = $_GET['search_term'] ?? null;
                $limit = $_GET['limit'] ?? 10;
                if ($limit <= 0) $limit = 10;

            ?>

            <?php if ($search_term != null): ?>

            <?php

                ### QUERY CONFIGURATION ###

                $query = "SELECT * FROM product P WHERE P.company_name LIKE '%$search_term%' LIMIT $limit";
                $arguments = null;
                $description = "Search Results for \"$search_term\", displaying top $limit result(s)";

                ### QUERY EXECUTION ###

                include "scripts/exec_query.php";
                $data = exec_query($query, $arguments);
                
                ### DATA DISPLAY ###

                include "scripts/display_data.php";

                #display description
                echo("<h3>$description</h3>\n");

                #display table of results
                echo construct_table($data, ['company_name', 'product_name', 'product_code', ], ["Manufacturer", "Name", "Product Code", ]);

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
