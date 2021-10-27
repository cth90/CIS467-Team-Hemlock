<?php
require_once("tree_info.php");
/**
 * This file contains the code for handling paypal donations
 */

// This function handles the PayPal IPN call
function aah_paypal_ipn() {
    aah_adopt_tree_by_id(aah_get_any_unadopted_tree());
}
add_action('aah_donation_ipn', 'aah_paypal_ipn');