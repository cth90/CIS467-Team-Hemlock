<?php

function aah_render_tree_info($tree_info)
{
    // todo render tree info
    return implode(", ", $tree_info);
}

function aah_render_tree_lookup()
{
    ob_start();
    ?>
    <div class="tree-lookup">
        <form action="" method="post" name="tree-lookup-form">
            <div class="form-field">
                <label>Tag: </label>
                <input name="tree_tag" type="text">
            </div>
            <input type="hidden" name="action" value="aah_get_tree_info">
            <button type="submit">Lookup Tree</button>
        </form>
    </div>
    <div class="tree-info" style="display:none">
        <label>ID: </label><div class="tree-id"></div><br>
        <label>Tag: </label><div class="tree-tag"></div><br>
        <label>DBH: </label><div class="tree-dbh"></div><br>
        <label>Lat: </label><div class="tree-lat"></div><br>
        <label>Long: </label><div class="tree-long"></div><br>
        <label>Loc: </label><div class="tree-loc"></div><br>
        <label>Notes: </label><div class="tree-notes"></div><br>

    </div>
    <script type="text/javascript">
        jQuery('form[name="tree-lookup-form"]').on('submit', function () {
            var form_data = jQuery(this).serializeArray();
            $.post(ajaxurl, form_data, function (response) {
                var div = $(".tree-info");
                $(div).find(".tree-id").html(response['id']);
                $(div).find(".tree-tag").html(response['tag']);
                $(div).find(".tree-dbh").html(response['dbh']);
                $(div).find(".tree-lat").html(response['latitude']);
                $(div).find(".tree-long").html(response['longitude']);
                $(div).find(".tree-loc").html(response['location_id']);
                $(div).find(".tree-notes").html(response['notes']);
                $(div).show();
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('tree_search', 'aah_render_tree_lookup');