<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
        <?php 
            $sql_test1 = "SELECT * FROM PRODUCT";

            $sql_test2 = "SELECT P.package_id, LR4.location_id, LR4.address
            FROM location_r4 LR4, package P
            WHERE LR4.location_id = P.location_id";

            $sql_test3 = "SELECT P.package_id, LR4.location_id, LR4.address
            FROM location_r4 LR4, package P
            WHERE P.location_id <> P.destination AND LR4.location_id = P.location_id";

            $sql_test4 = "SELECT H.location_id, H.product_code, COUNT(Pd.product_code) AS quantity
            FROM houses H, product Pd, location_r4 LR4
            WHERE H.product_code = Pd.product_code AND H.location_id = LR4.location_id
            GROUP BY H.location_id, H.product_code";

            $sql_testparam1 = "SELECT * 
            FROM product P
            WHERE P.company_name = ?";

            $sql_testparam2 = "SELECT S.location_id, S.product_code, S.quantity
            FROM supplies S, product Pd, location_r4 LR4
            WHERE S.product_code = Pd.product_code AND S.location_id = LR4.location_id AND S.location_id = ?
            GROUP BY S.location_id, S.product_code
            HAVING S.quantity < ?";

            $sql_testparam3 = "SELECT S.location_id, S.product_code, S.quantity
            FROM supplies S, product Pd, location_r4 LR4
            WHERE S.product_code = Pd.product_code AND S.location_id = LR4.location_id AND S.location_id = :locations
            GROUP BY S.location_id, S.product_code
            HAVING S.quantity < :threshold";

            include "scripts/connect.php";

            $dbc = new DBConnect("localhost:3306", "inventory");
            $dbc->set_login("project", "CPSC304!");
            if ($dbc->login()) {
                echo "Login successful <br>";
                
                function test_query($dbc, int $testnum, string $query, array $args = null) {
                    echo "<br> Test $testnum: <br>";
                    $stmt = $dbc->query($query, $args);
                    $data = $stmt->fetchAll();
                    foreach ($data as $row) {
                        //echo $row['product_code'] . " - " . $row['product_name'] . " - " . $row['company_name'] . "<br />\n";
                        echo json_encode($row)."<br />\n";
                    }
                }

                test_query($dbc, 1, $sql_test1);
                test_query($dbc, 2, $sql_test2);
                test_query($dbc, 3, $sql_test3);
                test_query($dbc, 4, $sql_test4);
                test_query($dbc, 5, $sql_testparam1, ["Mango Repub"]);
                test_query($dbc, 6, $sql_testparam1, ["Canton Canning Company"]);
                test_query($dbc, 7, $sql_testparam2, [103, 15]);
                test_query($dbc, 8, $sql_testparam3, ["locations" => 103, "threshold" => 10]);

                $dbc->disconnect();
            } else {
                echo "Login unsuccessful" . "<br>" . $dbc->errmsg . "<br>";
            }
        ?>
    </body>
</html>