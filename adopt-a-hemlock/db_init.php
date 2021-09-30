<?php
/**
 * This file contains the code for initializing the database for the plugin
 * using the same db as WordPress.
 */

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