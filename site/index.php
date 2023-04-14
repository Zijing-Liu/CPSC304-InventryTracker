<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
        <?php include "html/headernav.html"; ?>

        <div>
            <h2>Query List</h2>
            INSERT: <a href="/add_new.php">Add or Remove Products</a>, <a href="/update.php">Update Inventory</a> <br>
            DELETE: <a href="/add_new.php">Add or Remove Products</a> <br>
            UPDATE: <a href="/update_product.php">Update Product Information</a>, <a href="/update.php">Update Inventory</a> <br>
            Selection: <a href="/stock.php">Stock Information</a>, <a href="/product_search.php">Product Search</a>, <a href="/company_search.php">Company Search</a>, etc. <br>
            Projection: <a href="/projection.php">View Store Information</a> <br>
            Join: <a href="/package_track.php">Package Track</a>, <a href="/projection.php">View Store Information</a>, etc. <br>
            Aggregation (GROUP BY): <a href="/product_quantity.php">Product Quantity</a> <br>
            Aggregation (HAVING): <a href="/Supplies_count.php">Check Stores Supplying a Set of Products</a> <br>
            Aggregation (Nested): <a href="/short_supply.php">Supply Shortage</a>, <a href="/product_quantity.php">Product Quantity</a> <br>
            Division: <a href="/Supplies_count.php">Check Stores Supplying a Set of Products</a> <br>
        </div>

        <?php include "html/footer.html"; ?>

    </body>
</html>
