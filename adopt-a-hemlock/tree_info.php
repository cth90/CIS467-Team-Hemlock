<?php
/**
 * This file contains the functions for retrieving and updating info about trees from the database
 */

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
        'amt_donated' => '99,999,999.99',
        'anonymous' => 'false',
        'tree_id' => $tree_id
    );
    $query = 'INSERT INTO `aah_transactions` (name, payment_id, amt_donated, tree_id, anonymous)
SELECT %s, %d, %s, id, %s
FROM `aah_trees`
WHERE id = %s
AND id NOT IN (SELECT b.tree_id FROM `aah_transactions` b)';
}