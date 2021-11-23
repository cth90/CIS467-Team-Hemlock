<?php

// todo transaction lookup
function aah_render_transaction_lookup()
{
    ob_start();
    ?>
    <div class="tree-lookup">
        <form action="" method="post" class="tree-lookup-form">
            <div class="form-field">
                <label>Tag: </label>
                <input name="tree_tag" type="text">
            </div>
            <input type="hidden" name="action" value="aah_get_tree_info">
        </form>
        <button type="button" class="tree-lookup-button">Lookup Tree</button>
    </div>
    <div class="tree-info" style="display:none">
        <label>ID: </label>
        <div style="display:inline" class="tree-id"></div>
        <br>
        <label>Tag: </label>
        <div style="display:inline" class="tree-tag"></div>
        <br>
        <label>Adopted <input type="checkbox" class="tree-adopted" name="adopted" disabled></label>
        <br>
        <label>DBH: </label>
        <div style="display:inline" class="tree-dbh"></div>
        <br>
        <label>Lat: </label>
        <div style="display:inline" class="tree-lat"></div>
        <br>
        <label>Long: </label>
        <div style="display:inline" class="tree-long"></div>
        <br>
        <label>Loc: </label>
        <div style="display:inline" class="tree-loc"></div>
        <br>
        <label>Notes: </label>
        <div style="display:inline" class="tree-notes"></div>
        <br>
    </div>
    <script type="text/javascript">
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        jQuery(document).ready(function ($) {
            $('.tree-lookup-button').click(function () {
                var form_data = jQuery('.tree-lookup-form').serializeArray();
                $.post(ajaxurl, form_data, function (response) {
                    var div = $(".tree-info");
                    $(div).find(".tree-id").html(response['id']);
                    $(div).find(".tree-tag").html(response['tag']);
                    $(div).find(".tree-dbh").html(response['dbh']);
                    $(div).find(".tree-lat").html(response['latitude']);
                    $(div).find(".tree-long").html(response['longitude']);
                    $(div).find(".tree-loc").html(response['location']);
                    $(div).find(".tree-notes").html(response['notes']);
                    if (response['adopted'] == 1) {
                        $(div).find(".tree-adopted").prop('checked', true);
                    }
                    $(div).show();
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('tree_search', 'aah_render_transaction_lookup');
