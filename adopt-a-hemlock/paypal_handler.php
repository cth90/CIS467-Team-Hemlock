<?php
require_once("tree_info.php");
/**
 * This file contains the code for handling paypal donations
 */

// This function handles the PayPal IPN call
function aah_paypal_ipn()
{
    if (aah_paypal_ipn_handshake() == true) {

        if(!aah_handle_valid_donation()) {
            // Handling of transaction failed
            // todo handle error
        }

    } else {
        aah_handle_invalid_donation();
    }
}
add_action('admin_post_aah_donation_ipn', 'aah_paypal_ipn');
add_action('admin_post_nopriv_aah_donation_ipn', 'aah_paypal_ipn');

// This returns the PayPal IPN URL.
// Currently configured to use the Sandbox URL.
function aah_get_paypal_url() {
	return 'https://ipnpb.paypal.com/cgi-bin/webscr';
}

// Verify PayPal transaction
// Reference: https://developer.paypal.com/docs/api-basics/notifications/ipn/ht-ipn/
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
        return true;
    } else if (strcmp ($res, "INVALID") == 0) {
        // IPN invalid
        return false;
    }
}

function aah_handle_valid_donation() {
    $info['email'] = $_POST['payer_email'];
    $info['name'] = $_POST['first_name'] . " " . $_POST['last_name'];
    $info['amt_donated'] = $_POST['mc_gross'];
    $info['payment_id'] = $_POST['txn_id'];
    $info['adoption_id'] = md5( $_POST['txn_id'] . $_POST['payer_email']);


    if (!aah_create_new_transaction($info)) {
        // todo transaction creation failed
        return false;
    } else {
        // transaction creation succeeded
        aah_send_donation_email($info);
    }
}

// Attach image to picture
$file = plugin_dir_path(__FILE__) . 'thankyou.png';
$uid = 'thank-you-picture';
$name = 'thankyou.png';
global $phpmailer;
add_action( 'phpmailer_init', function(&$phpmailer)use($file, $uid, $name){
    $phpmailer->SMTPKeepAlive = true;
    $phpmailer->AddEmbeddedImage($file, $uid, $name);
});

function aah_send_donation_email($info) {
    $headers = array('Content-Type: text/html; charset=UTF-8', 'From: "Adopt-a-Hemlock" <info@adoptahemlock.org>');
    wp_mail($info['email'], 'Thank You for Donating', aah_get_email_text($info), $headers);
}

function aah_get_email_text($info) {

    $email_info = array(
        '%name%'=>$info['name'],
        '%amt_donated%'=>$info['amt_donated'],
        '%link%'=>get_site_url(null, 'adoption-information?a_id=' . $info['adoption_id'])
    );
    $text = file_get_contents(plugin_dir_path(__FILE__) . 'thank_you_email.txt');
    return strtr($text, $email_info);
}

function aah_handle_invalid_donation() {
    // todo handle invalid donation
}