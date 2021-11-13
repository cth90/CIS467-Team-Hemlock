<?php
/**
 * This file contains the code to display and handle the adoption page
 */

function aah_render_adoption_page()
{
    // Handle location selection form if it was submitted
    if (!empty($_POST['tree-selection-form'])) {
        // todo handle form
    }

    ob_start();
    if ($transaction = aah_get_transaction_info_by_aid($_POST['id'])) {
        if (!empty($transaction['tree_tag'])) {
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
                    <input type="hidden" value="<?php echo $_POST['id'] ?>">
                    <button type="submit" class="tree-selection-submit">Adopt</button>
                </form>
            </div>
            <?php
        }
    } else {
        echo "Transaction not found.";
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
    t.adoption_id as id,
    t.pdf_link as link,
    t.completed as completed,
    t.email as email,
    t.anonymous as anonymous
FROM
    `aah_transactions` as t
inner join `aah_trees` as tree on t.id = tree.tree_id
inner join `aah_locations` as loc on loc.id = t.location_id
WHERE 
    tree.adoption_id = %s';

    if (!($result = $wpdb->get_row($wpdb->prepare($sql, $aid), ARRAY_A))) {
        trigger_error("No matching transaction found.");
        return false;
    }

    return $result;

}