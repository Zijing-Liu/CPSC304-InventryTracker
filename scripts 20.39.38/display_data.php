<?php
# helper functions for displaying query result data

/*
 * Parameters:
 * $data            - table data
 * $arr_columns     - array of the fields to use as the columns, in order
 * $header_names    - array of strings to use as table headers, in order
 * $classname       - HTML class to give table, for styling
 * 
 * Returns:
 * - HTML for the table, suitable for direct echoing onto page
 */
function construct_table($data, $arr_columns = null, $header_names = null, $classname = null) {
    if ($arr_columns == null) {
        $arr_columns = array_keys(current($data));
    }

    if ($header_names == null) {
        $header_names = $arr_columns;
    }

    $html = '';

    if (count($data) > 0) {
        if ($classname != null) {
            $html .= '<table class="'.$classname.'">';
        } else {
            $html .= '<table>';
        }

        #headers
        $html .= '<thead><tr><th>';
        $html .= implode('</th><th>', $header_names);
        $html .= '</th></tr></thead>';

        #rows
        $html .= '<tbody>';
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($arr_columns as $colkey) {
                $html .= '<td>';
                $html .= htmlentities($row[$colkey]);
                $html .= '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
    }

    return $html;
}


?>