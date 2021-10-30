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

// Get one tree by its tag
function aah_get_tree_by_tag($tag)
{
    global $wpdb;
    $sql = 'SELECT a.* FROM `aah_trees` a WHERE a.tag = %s LIMIT 1';
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

// This returns info for a tree.
// Can return only unadopted trees, or any tree by tag.
// If unadopted is set to true, it will return a random unadopted tree
// or one that matches the location or tag.
function aah_get_tree_info($atts)
{
    // placeholder tree if no tree is returned
    $no_tree = array(
        'id'=>'none',
        'tag'=>'none',
        'dbh'=>'none',
        'latitude'=>'none',
        'longitude'=>'none',
        'notes'=>'none',
        'location_id'=>-1,
    );

    // If unadopted tree is requested
    if (isset($atts['unadopted']) && $atts['unadopted'] == true) {
        if (isset($atts['tag'])) {
            $tree_info = (aah_get_unadopted_tree_by_tag($atts['tag']) ?? $no_tree);
        } else if (isset($atts['location'])) {
            $tree_info = (aah_get_any_unadopted_tree_by_area($atts['tag']) ?? $no_tree);
        } else {
            $tree_info = (aah_get_any_unadopted_tree() ?? $no_tree);
        }
    } else { // otherwise get a tree by tag only
        $tree_info = (aah_get_tree_by_tag($atts['tag']) ?? $no_tree);
    }
    $tree_info['location'] = (aah_get_location_name($tree_info['location_id']) ?? 'Unknown');
    $tree_info['adopted'] = aah_get_tree_is_adopted($tree_info['id']);
    return $tree_info;
}
//add_shortcode('tree_info', 'aah_get_tree_by_shortcode');

// Get tree info using ajax hook
function aah_get_tree_info_ajax() {
    $params = array();
    if (isset($_POST['tree_tag'])) {
        $params['tag'] = $_POST['tree_tag'];
    }
    if (isset($_POST['tree_location'])) {
        $params['location'] = $_POST['tree_location'];
    }
    if (isset($_POST['tree_unadopted'])) {
        $params['unadopted'] = $_POST['tree_unadopted'];
    }
    $result = aah_get_tree_info($params);
    wp_send_json($result);
}
// Add the hooks for the ajax action
add_action('wp_ajax_aah_get_tree_info', 'aah_get_tree_info_ajax');
add_action('wp_ajax_nopriv_aah_get_tree_info', 'aah_get_tree_info_ajax');

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

// Get array of locations
function aah_get_locations() {
    global $wpdb;
    $sql = 'SELECT * FROM `aah_locations`';
    if (!($result = $wpdb->get_results($sql, ARRAY_A))) {
        trigger_error("No locations found.");
        return false;
    }
    return $result;
}

// Get location name by id
function aah_get_location_name($id) {
    if ($id < 0) { return 0; }
    global $wpdb;
    $sql = 'SELECT name FROM `aah_locations` WHERE id = %d';
    if (!($result = $wpdb->get_row($wpdb->prepare($sql, $id), ARRAY_N))) {
        trigger_error("No matching location found.");
        return 0;
    }
    return $result[0];
}

// Returns 1 if the tree specified by $id is adopted, else 0
function aah_get_tree_is_adopted($id)
{
    global $wpdb;
    $sql = 'SELECT 1 FROM `aah_transactions` WHERE tree_id = %d';
    return $wpdb->get_var($wpdb->prepare($sql, $id)) ?? 0;
}

// Return the number of trees in a specified location
function aah_get_tree_count_by_location($attr) {
    $count = 0;
    if (isset($attr['location'])) {
        global $wpdb;
        $sql = 'SELECT COUNT(id) FROM `aah_trees` WHERE location_id IN (SELECT id FROM `aah_locations` WHERE name = %s)';
        $count = $wpdb->get_var($wpdb->prepare($sql, $attr['location'])) ?? 0;
    }
    return $count;
}
add_shortcode('tree_count', 'aah_get_tree_count_by_location');

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

// Create a new transaction
function aah_create_new_transaction($info) {
    $transaction_info = array(
        'email' => $info['email'],
        'payment_id' => $info['payment_id'],
        'adoption_id' => $info['adoption_id'],
        'amt_donated' => $info['dnt_amt'],
        'anonymous' => 0,
        'completed' => 0
    );

    return aah_insert_transaction($transaction_info);
}