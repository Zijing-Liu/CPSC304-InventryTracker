<?php

include "connect.php";

### QUERY EXECUTION ###

# Expectations:
# $query        - string that is query to execute
# $arguments    - associative array of arguments for query placeholders, should
#                 be null if no placeholders
#
# Will produce:
# $data         - array containing all the rows that results from the query

function exec_query($query, $arguments) {
    $dbc = new DBConnect("localhost", "Inventory");
    $dbc->set_login("Celine", "CPSC304!");
    if ($dbc->login()) {
        $stmt = $dbc->query($query, $arguments);
        $data = $stmt->fetchAll();
        
        $dbc->disconnect();

        return $data;
    } else {
        return "Login unsuccessful" . "<br>" . $dbc->errmsg . "<br>";
    }
}

?>