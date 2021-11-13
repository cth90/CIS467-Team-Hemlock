<?php
/**
 * This file contains the code to display and handle the adoption page
 */
require_once 'tree_info.php';
require_once 'pdf_generator.php';

function aah_render_adoption_page()
{
    ob_start();
    if ($transaction = aah_get_transaction_info_by_aid($_GET['a_id'])) {

        // Handle location selection form if it was submitted
        if (!empty($_POST['tree-locations'])) {
            aah_adopt_tree($transaction['transaction_id'], $_POST['tree-locations'], boolval($_POST['anonymous']));
            echo "Tree adoption failed: " . $_POST['tree-selection-form'];
            // update transaction info
            $transaction = aah_get_transaction_info_by_aid($_GET['a_id']);
        }

        // Show details of a completed transaction
        if ($transaction['completed']) {
            ?>
            <div class="adoption-info">
                <label>Tree Tag: <input name="tree_tag" value="<?php echo $transaction['tree_tag'] ?>" type="text"
                                        disabled></label>
                <br><label>Location: <input name="tree_location" value="<?php echo $transaction['location_name'] ?>"
                                            type="text" disabled></label>
                <br><label>Coordinates: <input name="tree_coords"
                                               value="<?php echo $transaction['latitude'] . ", " . $transaction['longitude'] ?>"
                                               type="text" disabled></label>
                <br><label>Notes: <input name="tree_notes" value="<?php echo $transaction['notes'] ?>" type="text"
                                         disabled></label>
                <br><label>PDF
                    Certificate: <?php echo "<a href='" . $transaction['link'] . "'>" . $transaction['link'] . "</a>"; ?></label>
            </div>
            <?php
        } else {
            // complete transaction
            $locations = aah_get_locations();
            ?>
            <div class="tree-selection">
                <p>You have not yet adopted a tree. Please select a location from the dropdown below (select any if you
                    want a location to be selected for you).</p>
                <form method="post" class="tree-selection-form" name="tree-selection-form">
                    <select name="tree-locations" id="tree-locations">
                        <option label="Any" value="any" selected>
                            <?php
                            foreach ($locations as $location) {
                                echo "<option label='" . $location['name'] . "' value='" . $location['id'] . "'>";
                            }
                            ?>
                    </select>
                    <br><label>Hide Name from Donor List: <input type="checkbox" name="anonymous" value="1"></label>
                    <input type="hidden" name="a_id" value="<?php echo $_POST['a_id'] ?>">
                    <br><button type="submit" class="tree-selection-submit">Adopt</button>
                </form>
            </div>
            <?php
        }
    } else {
        echo "Transaction id " . $_GET['a_id'] . " not found.";
    }

    return ob_get_clean();
}
add_shortcode('adopt-tree', 'aah_render_adoption_page');

function aah_get_transaction_info_by_aid($aid)
{
    global $wpdb;

    $sql = 'SELECT
	t.`name` AS name,
	tree.`tag` as tree_tag,
	loc.`name` as location_name,
	tree.`longitude` as longitude,
	tree.`latitude` as latitude,
    tree.`notes` as notes,
    t.id as transaction_id,
    t.adoption_id as a_id,
    t.pdf_link as link,
    t.completed as completed,
    t.email as email,
    t.anonymous as anonymous,
    tree.id as tree_id
FROM
    `aah_transactions` as t
left join `aah_trees` as tree on tree.id = t.tree_id
left join `aah_locations` as loc on loc.id = tree.location_id
WHERE 
    t.adoption_id = %s';

    if (!($result = $wpdb->get_row($wpdb->prepare($sql, $aid), ARRAY_A))) {
        trigger_error("No matching transaction found.");
        return false;
    }

    return $result;

}

// Adopt the tree
function aah_adopt_tree($transaction_id, $location, $anon) {
    global $wpdb;

    if ($location == "any") {
        $tree = aah_get_any_unadopted_tree();
    } else {
        $tree = aah_get_any_unadopted_tree_by_area($location);
    }

    $updated_t = array(
        'anonymous'=>$anon,
        'completed'=>1,
        'tree_id'=>$tree['id']
    );

    // update transaction
    if(!($wpdb->update("aah_transactions", $updated_t, array("id"=>$transaction_id)))) {
        trigger_error("Unable to adopt tree.");
        return false;
    }

    // generate pdf
    if (!($pdf_link = aah_get_pdf_by_transaction($transaction_id))) {
        trigger_error("Unable to generate pdf");
        // todo handle pdf error
    } else {
        // update transaction with pdf link
        if (!($wpdb->update("aah_transactions", array("pdf_link" => $pdf_link), array("id" => $transaction_id)))) {
            trigger_error("Unable to update pdf link.");
        }
    }

    return true;
}