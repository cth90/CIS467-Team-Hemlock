<?php
/**
 * This file contains the code for initializing the database for the plugin
 * using the same db as WordPress.
 */

function create_tables($sql_file) {

    // global variable for wordpress db class
    global $wpdb;

    // load file
    $queries = file_get_contents($sql_file);

    // split into array
    $queries = explode(";", $queries);

    // run queries
    foreach ($queries as $query) {
        if ( !( $wpdb->query($query) ) ) {
            // if a query fails
            echo "<div><h1>Query failed:</h1><br><p>$query</p></div>";
            break;
        }
    }
}