<?php
require_once("tree_info.php");
/**
 * This file contains the code for handling paypal donations
 */

// This function handles the PayPal IPN call
function aah_paypal_ipn() {
    aah_paypal_ipn_handshake();
}
add_action('admin_post_aah_donation_ipn', 'aah_paypal_ipn');
add_action('admin_post_nopriv_aah_donation_ipn', 'aah_paypal_ipn');

// This returns the PayPal IPN URL.
// Currently configured to use the Sandbox URL.
function aah_get_paypal_url() {
    // todo make this an admin panel option
    return 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
}

function aah_paypal_ipn_handshake() {

    // Read POST data
    $raw_post_data = file_get_contents('php://input');
    $raw_post_array = explode('&', $raw_post_data);
    $myPost = array();
    foreach ($raw_post_array as $keyval) {
        $keyval = explode('=', $keyval);
        if (count($keyval)==2) {
            $myPost[$keyval[0]] = urldecode($keyval[1]);
        }
    }

    // Read IPN message from PayPal and modify it to verify with PayPal
    $req = 'cmd=_notify-validate';
    foreach($myPost as $key => $value) {
        $value = urlencode($value);
        $req .= "&$key=$value";
    }

    // POST IPN data back to PayPal to validate
    $ch = curl_init(aah_get_paypal_url());
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
    if ( !($res = curl_exec($ch)) ) {
        curl_close($ch);
        return false;
    }
    curl_close($ch);

    // inspect IPN validation result and act accordingly
    if (strcmp ($res, "VERIFIED") == 0) {
        // The IPN is verified
        // todo valid IPN
        aah_handle_valid_donation();
    } else if (strcmp ($res, "INVALID") == 0) {
        // IPN invalid
        // todo invalid IPN
        aah_handle_invalid_donation();
    }
}

function aah_handle_valid_donation() {
    aah_adopt_tree_by_id(aah_get_any_unadopted_tree()['id']);
}

function aah_handle_invalid_donation() {

}