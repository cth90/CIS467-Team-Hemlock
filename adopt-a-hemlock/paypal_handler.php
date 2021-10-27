<?php
require_once("tree_info.php");
/**
 * This file contains the code for handling paypal donations
 */

// This function handles the PayPal IPN call
function aah_paypal_ipn() {
    $id = aah_get_any_unadopted_tree()['id'];
    $result = aah_adopt_tree_by_id($id);
}
add_action('admin_post_aah_donation_ipn', 'aah_paypal_ipn');
add_action('admin_post_nopriv_aah_donation_ipn', 'aah_paypal_ipn');