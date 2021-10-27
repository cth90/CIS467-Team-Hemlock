<?php
require_once("tree_info.php");
/**
 * This file contains the code for handling paypal donations
 */

// This function handles the PayPal IPN call
function aah_paypal_ipn() {
    $id = aah_get_any_unadopted_tree();
    echo "Tree ID: $id<br>";
    $result = aah_adopt_tree_by_id($id);
    echo "Result: $result";
    ?>
    <script type="text/javascript">
        alert("Donation received");
    </script>
    <?php
}
add_action('aah_donation_ipn', 'aah_paypal_ipn');