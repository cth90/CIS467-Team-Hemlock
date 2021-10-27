<?php
require_once("tree_info.php");
/**
 * This file contains the code for handling paypal donations
 */

// This function handles the PayPal IPN call
function aah_paypal_ipn() {
    $id = aah_get_any_unadopted_tree();
    $result = aah_adopt_tree_by_id($id);
    ?>
    <script type="text/javascript">
        <?php
            echo "var tree_id = '$id'";
            echo "var result = '$result'";
        ?>
        alert("Donation received. Tree ID: " . id . " and result: " . result)
    </script>
    <?php
}
add_action('aah_donation_ipn', 'aah_paypal_ipn');