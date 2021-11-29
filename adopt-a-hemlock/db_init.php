<?php
/**
 * This file contains the code for initializing the database for the plugin
 * using the same db as WordPress.
 */

// This maps the headers from the csv file to table fields
const CSV_HEADERS_MAP = array(
    'tag' => 'tag', 'tag #' => 'tag', 'dbh' => 'dbh', 'latitude' => 'latitude', 'lat' => 'latitude',
    'longitude' => 'longitude', 'long' => 'longitude', 'notes' => 'notes',
    'parcel' => 'parcel', 'parcel #' => 'parcel','address' => 'address', 'starting address' => 'address'
);

// This is a list of required fields for the SQL query with their indexes
const REQUIRED_FIELDS_LIST = array(
  'tag'=>0, 'dbh'=>1, 'latitude'=>2, 'longitude'=>3, 'notes'=>4, 'parcel'=>5, 'address'=>6
);

const SQL_INSERT_TEMPLATE = 'INSERT INTO aah_trees (tag, dbh, latitude, longitude, notes, location_id)
SELECT %s, %d, %s, %s, %s, id
  FROM aah_locations
 WHERE parcel = %s or address = %s
 LIMIT 1';

function aah_create_tables($sql_file)
{

    // global variable for wordpress db class
    global $wpdb;

    // load file
    if (!($queries = file_get_contents($sql_file))) {
        return array("success"=>"false", "error"=>"Failed at file_get_contents");
    }

    // split into array
    if (!($queries = explode(";", $queries))) {
        return array("success"=>"false", "error"=>"Failed at explode.");
    }

    // run queries
    foreach ($queries as $query) {
        if (strlen($query) > 0) {
            if (!($wpdb->query($query))) {
                // if a query fails
                return array("success"=>"false", "error"=>"Failed at $query");
            }
        }
    }

    return array("success"=>"true", "error"=>"none");
}

// Read given csv file and import trees into database
// Returns -1 on read error, otherwise returns count of trees added
function aah_read_csv($csv_file) {
    if (($handle = fopen($csv_file, "r")) !== FALSE) {

        // Get headers line first
        if (($data = fgetcsv($handle)) == FALSE) {
            // Error
            trigger_error("Could not read CSV file.");
            return -1;
        }

        $line_headers = aah_get_line_headers($data);
        if ($line_headers == FALSE) {
            // Error
            trigger_error("Could not read CSV headers.");
            return -1;
        }

        $row_count = 0;
        // Read each line and parse it
        while (($data = fgetcsv($handle)) !== FALSE) {
            if (($tree_info = aah_parse_line($data, $line_headers)) != FALSE) {
                if (aah_insert_tree($tree_info) != 1) {
                    $error_line = $row_count + 1;
                    trigger_error("Error adding tree at $error_line");
                } else {
                    $row_count++;
                }
            } else  {
                // Line parse error, row count + 2 is to account for the header line
                // Continues execution
                $error_line = $row_count + 2;
                trigger_error("Error parsing tree at line $error_line");
            }
        }
        fclose($handle);

        return $row_count;

    } else {
        // Error
        trigger_error("Could not open CSV file.");
        return -1;
    }
}

// Parse the line headers from the csv file
function aah_get_line_headers($first_line) {
    $line_headers = array();

    foreach ($first_line as $header) {
        $modified_header = strtolower(trim($header));
        $line_headers[] = (CSV_HEADERS_MAP[$modified_header] ?? 'ignored');
    }

    return $line_headers;
}

// Parse a single line from the csv file
function aah_parse_line($line, $headers) {
    $values = array(0=>'',1=>'',2=>'',3=>'',4=>'',5=>'',6=>'');
    for ($i = 0; $i < count($headers); $i++) {
        if (array_key_exists($headers[$i], REQUIRED_FIELDS_LIST)) {
            $values[REQUIRED_FIELDS_LIST[$headers[$i]]] = ($line[$i] ?? '');
        }
    }
    return $values;
}

// Insert a tree into the database
function aah_insert_tree($tree_info) {

    if (strtoupper(substr($tree_info[0], 0, 1)) === "S") {
        $tree_info['notes'] = "S not shown on physical tag. Tag is plastic. " . $tree_info['notes'];
    }
    global $wpdb;
    return $wpdb->query($wpdb->prepare(SQL_INSERT_TEMPLATE, $tree_info));
}