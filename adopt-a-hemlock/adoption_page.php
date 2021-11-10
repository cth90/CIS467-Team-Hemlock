<?php
/**
 * This file contains the code to display and handle the adoption page
 */

function aah_render_adoption_page()
{
    ob_start();
    ?>
            <div class="adoption-info">
                <label>Tree Tag: <input name="tree_tag" type="text" disabled></label>
                <label>Location: <input name="tree_location" type="text" disabled></label>
                <label>Coordinates: <input name="tree_coords" type="text" disabled></label>
                <label>Notes: <input name="tree_notes" type="text" disabled></label>
                <label>PDF Certificate: <?php if (!($result = aah_get_pdf_link_by_aid($_POST['id']))) {
                    echo "n/a";
                    } else {
                    echo "<a href='$result'>$result</a>";
                    }?></label>
            </div>
    <?php
    return ob_get_clean();
}
add_shortcode('adopt-tree', 'aah_render_adoption_page');