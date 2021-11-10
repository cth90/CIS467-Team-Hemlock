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


    $transaction = aah_get_transaction_info_by_aid($_POST['id']);
    ob_start();
    if (!empty($transaction['tree_tag'])) {
    ?>
            <div class="adoption-info">
                <label>Tree Tag: <input name="tree_tag" value="<?php echo $transaction['tree_tag'] ?>" type="text" disabled></label>
                <label>Location: <input name="tree_location" value="<?php echo $transaction['location_name'] ?>" type="text" disabled></label>
                <label>Coordinates: <input name="tree_coords" value="<?php echo $transaction['latitude'] . ", " . $transaction['longitude'] ?>" type="text" disabled></label>
                <label>Notes: <input name="tree_notes" value="<?php echo $transaction['notes'] ?>" type="text" disabled></label>
                <label>PDF Certificate: <?php echo "<a href='" . $transaction['link'] . "'>" . $transaction['link'] . "</a>"; ?></label>
            </div>
    <?php
    } else {
        $locations = aah_get_locations();
        ?>
            <div class="tree-selection">
                <p>You have not yet adopted a tree. Please select a location from the dropdown below (select any if you want a location to be selected for you).</p>
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
    return ob_get_clean();
}
add_shortcode('adopt-tree', 'aah_render_adoption_page');

function aah_get_transaction_info_by_aid($aid): array
{
    // todo get transaction info
    $info = array(
        'name'=>'Placeholder Name',
        'date'=>date('m/d/Y'),
        'tree_tag'=>'0000',
        'location_name'=>'Wherever',
        'longitude'=>'9999999.9999',
        'latitude'=>'9999999.9999',
        'notes'=>'',
        'link'=>''
    );
    return $info;
}