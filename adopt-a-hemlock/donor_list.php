<?php

// Return an array of donor names by specified location name
function aah_get_donors_by_location($loc)
{
    global $wpdb;
    $sql = 'SELECT t.name FROM `aah_transactions` as t
left join `aah_trees` as tree on tree.id = t.tree_id
left join `aah_locations` as loc on loc.id = tree.location_id
WHERE loc.name = %s AND t.anonymous = 0';
    return $wpdb->get_results($wpdb->prepare($sql, $loc), ARRAY_A);
}

function aah_render_donor_list($attr)
{
    ob_start();
    echo '<ul style="list-style-type:none;margin:0;padding:0">';
    if (isset($attr['location'])) {
        $donors = aah_get_donors_by_location($attr['location']);

        foreach($donors as $donor) {
            $name = $donor['name'];
            echo "<li>$name</li>";
        }
    }
    echo '</ul>';
    return ob_get_clean();
}

add_shortcode('donor_list', 'aah_render_donor_list');