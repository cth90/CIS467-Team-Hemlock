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
        $e = error_get_last()['message'];
        return "<div><h3>Table creation failed!</h3><br><p>Failed at file_get_contents</p><p>$e</p></div>";
    }

    // split into array
    if (!($queries = explode(";", $queries))) {
        return "<div><h3>Table creation failed!</h3><br><p>Failed at explode</p></div>";
    }

    // run queries
    foreach ($queries as $query) {
        if (strlen($query) > 0) {
            if (!($wpdb->query($query))) {
                // if a query fails
                return "<div><h3>Query failed:</h3><br><p>$query</p></div>";
            }
        }
    }

    return "<div><h3>Successfully created tables!</h3></div>";
}

function aah_create_tables_ajax($sql_file) {
    $result = aah_create_tables($sql_file);
    if ($result != "<div><h3>Successfully created tables!</h3></div>") {
        return array("success"=>"true", "error"=>"none");
    } else {
        return array("success"=>"false", "error"=>$result);
    }
}