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

            <h3>View Store Information</h3>

            <form action="projection.php" method="get">
                <fieldset>
                    <legend>Choose which fields to display:</legend>
                    
                    <input type="checkbox" id="location_name" name="location_name" checked="checked">
                    <label for="location_name">Store Name</label>

                    <br>

                    <input type="checkbox" id="address" name="address" checked="checked">
                    <label for="address">Address</label>

                    <br>

                    <input type="checkbox" id="opening" name="opening" checked="checked">
                    <label for="opening">Opening Hours</label>

                    <br>

                    <input type="checkbox" id="phone_number" name="phone_number" checked="checked">
                    <label for="phone_number">Phone Number</label>

                    <br>

                    <input type="checkbox" id="capacity" name="capacity" checked="checked">
                    <label for="capacity">Capacity</label>

                    <br>

                    <input type="checkbox" id="delivery_hours" name="delivery_hours" checked="checked">
                    <label for="delivery_hours">Delivery Hours</label>

                    <br>

                    <input type="checkbox" id="company_name" name="company_name" checked="checked">
                    <label for="company_name">Operating Company Name</label>

                    <br>

                    <p>No selection defaults to all fields displayed.</p>

                    <input type="submit">
                </fieldset>
            </form>

        </div>
        
        <hr>

        <div class="output">

            <h2>Output</h2>

            <?php 
                ### PARAMETERS ###

                #print_r($_GET); echo '<br>';

                $field_list = ['location_id'];
                $field_names = ['Location ID'];
                
                #parameters from coalescing superglobal
                $location_name = (($_GET['location_name'] ?? '') == 'on');
                if ($location_name) {
                    $field_list[] = 'location_name';
                    $field_names[] = 'Store Name';
                }

                $address = (($_GET['address'] ?? '') == 'on');
                if ($address) {
                    $field_list[] = 'address';
                    $field_names[] = 'Address';
                }
                $opening = (($_GET['opening'] ?? '') == 'on');
                if ($opening) {
                    $field_list[] = 'opening_hours_start';
                    $field_list[] = 'opening_hours_end';
                    $field_names[] = 'Opening Hours Start';
                    $field_names[] = 'Opening Hours End';
                }

                $phone_number = (($_GET['phone_number'] ?? '') == 'on');
                if ($phone_number) {
                    $field_list[] = 'area_code';
                    $field_list[] = 'phone_number';
                    $field_names[] = 'Area Code';
                    $field_names[] = 'Phone Number';
                }

                $capacity = (($_GET['capacity'] ?? '') == 'on');
                if ($capacity) {
                    $field_list[] = 'capacity';
                    $field_names[] = 'Capacity';
                }

                $delivery_hours = (($_GET['delivery_hours'] ?? '') == 'on');
                if ($delivery_hours) {
                    $field_list[] = 'delivery_hours_start';
                    $field_list[] = 'delivery_hours_end';
                    $field_list[] = 'delivery_hours_length';
                    $field_names[] = 'Delivery Hours Start';
                    $field_names[] = 'Delivery Hours End';
                    $field_names[] = 'Delivery Hours Length';
                }

                $company_name = (($_GET['company_name'] ?? '') == 'on');
                if ($company_name) {
                    $field_list[] = 'company_name';
                    $field_names[] = 'Company Name';
                }
                
                #print_r($field_list);
                
            ?>

            <?php if (count($field_list) > 1): ?>

            <?php

                $filter_list = [];

                foreach ($field_list as $field) {
                    if ($field == 'location_id') $filter_list[] = 'LR4.location_id';
                    elseif ($field == 'address') $filter_list[] = 'LR4.address';
                    elseif ($field == 'delivery_hours_start') $filter_list[] = 'LR4.delivery_hours_start';
                    elseif ($field == 'delivery_hours_end') $filter_list[] = 'LR4.delivery_hours_end';
                    else $filter_list[] = $field;
                }

                $filters = implode(', ', $filter_list);

                ### QUERY CONFIGURATION ###

                $query = "SELECT $filters
                FROM store S, location_r1 LR1, location_r3 LR3, location_r4 LR4 
                WHERE LR4.location_id = S.location_id 
                AND LR4.address = LR1.address 
                AND LR4.delivery_hours_start = LR3.delivery_hours_start 
                AND LR4.delivery_hours_end = LR3.delivery_hours_end";
                $arguments = null;
                $description = "Displaying filtered location information";

                ### QUERY EXECUTION ###

                include "scripts/exec_query.php";
                $data = exec_query($query, $arguments);
                
                ### DATA DISPLAY ###

                include "scripts/display_data.php";

                #display description
                echo("<h3>$description</h3>\n");

                #display table of results
                echo construct_table($data, $field_list, $field_names);

                if (count($data) == 0) {
                    echo "No results";
                }

            ?>

            <?php else: ?>

            <?php echo "<h3>No selection</h3>\n"; ?>

            <?php endif ?>

        </div>

        <?php include "html/footer.html"; ?>

    </body>
</html>
