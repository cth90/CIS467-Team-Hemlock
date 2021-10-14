<?php
/**
 * This file contains the functions for retrieving and updating info about trees from the database
 */

require 'tree_info_view.php';

// Get one unadopted tree
function aah_get_any_unadopted_tree()
{
    global $wpdb;
    $sql = 'SELECT a.* FROM `aah_trees` a WHERE a.id NOT IN (SELECT b.tree_id FROM `aah_transactions` b) LIMIT 1';
    if (!($result = $wpdb->get_row($sql, ARRAY_A))) {
        trigger_error("No tree found.");
        return false;
    }
    return $result;
}

// Get one unadopted tree by its tag
function aah_get_unadopted_tree_by_tag($tag)
{
    global $wpdb;
    $sql = 'SELECT a.* FROM `aah_trees` a WHERE a.tag = %s AND a.id NOT IN (SELECT b.tree_id FROM `aah_transactions` b) LIMIT 1';
    if (!($result = $wpdb->get_row($wpdb->prepare($sql, $tag), ARRAY_A))) {
        trigger_error("No tree found.");
        return false;
    }
    return $result;
}

// Get one unadopted tree by area, using any of area id, name, parcel, or address
function aah_get_any_unadopted_tree_by_area($area)
{
    global $wpdb;
    $sql = 'SELECT a.* FROM `aah_trees` a 
WHERE a.id NOT IN (SELECT b.tree_id FROM `aah_transactions` b) AND a.location_id IN (
SELECT c.id FROM `aah_locations` c WHERE %s IN (id, name, parcel, address)) LIMIT 1';
    if (!($result = $wpdb->get_row($wpdb->prepare($sql, $area), ARRAY_A))) {
        trigger_error("No tree found.");
        return false;
    }
    return $result;
}

// This is the shortcode function called when the unadopted_tree shortcode is used.
// Accepts a tag, location, or nothing, in that order of priority.
function aah_get_tree_by_shortcode($atts)
{
    // todo placeholder tree
    $no_tree = array();
    if (isset($atts['tag'])) {
        $tree_info = (aah_get_unadopted_tree_by_tag($atts['tag']) ?? $no_tree);
    } else if (isset($atts['location'])) {
        $tree_info = (aah_get_unadopted_tree_by_tag($atts['tag']) ?? $no_tree);
    } else {
        $tree_info = (aah_get_any_unadopted_tree() ?? $no_tree);
    }
    return aah_render_tree_info($tree_info);
}
add_shortcode('unadopted_tree', 'aah_get_tree_by_shortcode');

// Get all adopted trees
function aah_get_all_adopted_trees()
{
    global $wpdb;
    $sql = 'SELECT a.* FROM `aah_trees` a WHERE a.id IN (SELECT b.tree_id FROM `aah_transactions` b)';
    if (!($result = $wpdb->get_results($sql, ARRAY_A))) {
        trigger_error("No trees found.");
        return false;
    }
    return $result;
}

// Create a dummy transaction to adopt a tree by its id (not tag) for development purposes
function aah_adopt_tree_by_id($tree_id)
{
    $transaction_info = array(
        'name' => 'Nyatasha Nyanners',
        'payment_id' => 9999,
        'amt_donated' => '99999999.99',
        'tree_id' => $tree_id,
        'anonymous' => 'false'
    );
    return aah_insert_transaction($transaction_info);
}

// Insert transaction info into db
function aah_insert_transaction($transaction_info)
{
    // todo add checks to $transaction_info
    global $wpdb;
    $result = $wpdb->insert('aah_transactions', $transaction_info);
    if (!$result) {
        trigger_error("Transaction creation failed.");
    }
    return $result;
}