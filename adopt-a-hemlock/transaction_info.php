<?php
require_once 'adoption_page.php';

// todo transaction lookup
function aah_render_transaction_lookup()
{
    ?>
    <table>
        <tr>
            <td>
                <h2>Search Transactions</h2>
                <p>You can search by adoption ID or tree tag.</p>
                <div class="transaction-lookup">
                    <form action="" method="post" class="transaction-lookup-form">
                        <div class="form-field">
                            <input name="search-key" type="text"><br>
                            <label>Tree Tag <input type="radio" id="tag-search-type" name="search-type" value="tree-tag"
                                                   checked></label>
                            <label>Adoption ID <input type="radio" id="id-search-type" name="search-type"
                                                      value="aid"></label><br>
                            <button type="submit" class="transaction-lookup-button">Search</button>
                        </div>
                    </form>
                </div>
                <br>

                <?php
                // if the search was submitted
                if (isset($_POST['search-key']) and $_POST['search-type']) {
                    if ($_POST['search-type'] == 'aid')
                        $transaction = aah_get_transaction_info_by_aid($_POST['search-key']);
                    else {
                        $transaction = aah_get_transaction_info_by_tag($_POST['search-key']);
                    }

                    // If transaction was edited
                    if (isset($_POST['edited'])) {
                        $edited_transaction = array();
                        $transaction_id = $_POST['transaction_id'];
                        if (!empty($_POST['tree_tag']) and $_POST['tree_tag'] != $transaction['tree_tag']) {
                            if (!($edited_transaction['tree_id'] = aah_get_unadopted_tree_by_tag($_POST['tree_tag'])['id'])) {
                                echo "<h2>Reassigned Tree Tag Not Found!</h2>";
                            }
                        }
                        if (!empty($_POST['name']) and $_POST['name'] != $transaction['name']) {
                            $edited_transaction['name'] = $_POST['name'];
                        }
                        if (!empty($_POST['email']) and $_POST['email'] != $transaction['email']) {
                            $edited_transaction['email'] = $_POST['email'];
                        }

                        $edited_transaction['anonymous'] = !empty($_POST['anonymous']);

                        if (!(aah_edit_transaction($transaction_id, $edited_transaction))) {
                            echo "<br><h2>Transaction Edit Failed!</h2>";
                        } else {
                            echo "<br><h2>Transaction Successfully Edited!</h2>";
                        }

                        if ($_POST['search-type'] == 'aid')
                            $transaction = aah_get_transaction_info_by_aid($_POST['search-key']);
                        else {
                            $transaction = aah_get_transaction_info_by_tag($_POST['search-key']);
                        }

                    }

                    if ($transaction) {
                        ?>
                        <br>
                        <div class="transaction-info-form">
                            <form action="" method="post" class="transaction-edit-form">
                                <label>Adoption ID: <a
                                            href="<?php echo site_url('adoption-information') . "?a_id=" . $transaction['a_id']; ?>"><?php echo $transaction['a_id']; ?></a></label>
                                <br><label>Tree Tag: <input name="tree_tag" id="tree_tag"
                                                            value="<?php echo $transaction['tree_tag']; ?>"></label>
                                <br><label>Name: <input name="name" id="name"
                                                        value="<?php echo $transaction['name']; ?>"></label>
                                <br><label>Email: <input name="email" id="email"
                                                         value="<?php echo $transaction['email']; ?>"></label>
                                <br><label>Anonymous: <input type="checkbox" name="anonymous" id="anonymous"
                                                             value="1" <?php
                                    if ($transaction['anonymous']) {
                                        echo "checked";
                                    }
                                    ?>></label>
                                <input type="hidden" name="transaction_id" id="transaction_id"
                                       value="<?php echo $transaction['transaction_id']; ?>">
                                <input type="hidden" name="search-key" id="search-key"
                                       value="<?php echo $_POST['search-key']; ?>">
                                <input type="hidden" name="search-type" id="search-type"
                                       value="<?php echo $_POST['search-type']; ?>">
                                <input type="hidden" name="edited" id="edited" value="1">
                                <br>
                                <button type="submit" class="transaction-edit-button">Submit Edits</button>
                            </form>
                        </div>
                        <?php
                    }
                } ?>
                <br><br>
            </td>
        </tr>
        <tr>
            <td>
                <h2>Add New Tree</h2>
                <p>You can add a new tree to the database using this form.</p>
                <?php

                $locations = aah_get_locations();

                // add new tree
                if ($_POST['add-new']) {
                    $tree_info = array(
                        'tag' => $_POST['tree_tag'],
                        'dbh' => $_POST['tree_dbh'],
                        'latitude' => $_POST['tree_lat'],
                        'longitude' => $_POST['tree_long'],
                        'location_id' => $_POST['tree_loc'],
                        'notes' => $_POST['tree_notes']
                    );

                    global $wpdb;

                    $result = $wpdb->insert('aah_trees', $tree_info);
                    if (!$result) {
                        trigger_error("Tree creation failed.");
                        echo "<h3>Tree Creation Failed!</h3>";
                    } else {
                        echo "<h3>Tree Created Successfully!</h3>";
                    }

                }

                ?>
                <div class="tree-add">
                    <form action="" method="post" class="tree-add-form">
                        <label>Tag: <input type="text" id="tree_tag" name="tree_tag"></label><br>
                        <label>Location: <select name="tree_loc" id="tree_loc">
                                <?php
                                foreach ($locations as $location) {
                                    echo '<option label="' . $location['name'] . '" value="' . $location['id'] . '">';
                                }
                                ?>
                            </select>
                        </label><br>
                        <label>DBH: <input type="text" id="tree_dbh" name="tree_dbh"></label><br>
                        <label>Longitude: <input type="text" id="tree_long" name="tree_long"></label><br>
                        <label>Latitude: <input type="text" id="tree_lat" name="tree_lat"></label><br>
                        <label>Notes: <input type="text" id="tree_notes" name="tree_notes"></label><br>
                        <input type="hidden" id="add-new" name="add-new" value="1">
                        <button type="submit" class="tree-add-button">Add Tree</button>
                    </form>
                </div>
                <br><br>
            </td>
        </tr>
        <tr>
            <td>
                <h2>Delete Tree</h2>
                <p>You can delete a tree from the database using this form.<br>Make sure to reassign any transactions attached to this tree first!</p>
                <?php
                    // delete the tree
                    if ($_POST['delete-tree'] AND !empty($_POST['tree_tag'])) {
                        global $wpdb;

                        if(!($result = $wpdb->delete('aah_trees', array('tag'=>$_POST['tree_tag'])))) {
                            echo "<h3>Tree Deletion Failed!</h3>";
                        } else {
                            echo "<h3>Tree Deleted Successfully!</h3>";
                        }
                    }
                ?>
                <div class="tree-delete">
                    <form action="" method="post" class="tree-delete-form">
                        <label>Tag: <input type="text" id="tree_tag" name="tree_tag"></label><br>
                        <input type="hidden" id="delete-tree" name="delete-tree" value="1">
                        <button type="submit" class="tree-delete-button">Delete Tree</button>
                    </form>
                </div>
            </td>
        </tr>
    </table>
    <?php
}

// edit a given transaction
function aah_edit_transaction($transaction_id, array $edited_transaction)
{
    global $wpdb;

    $edited_transaction['pdf_link'] = "";

    // update transaction
    if (!($wpdb->update("aah_transactions", $edited_transaction, array("id" => $transaction_id)))) {
        trigger_error("Unable to edit transaction.");
        return false;
    }
    return true;
}

// This adds the menu page for transaction management
function aah_configure_transaction_info_page()
{
    add_menu_page("Adopt-a-Hemlock Management", "Adopt-a-Hemlock Management",
        "manage_options", "adopt-a-hemlock-transaction-info", "aah_render_transaction_lookup");
}

// hook to add settings to admin menu
add_action('admin_menu', 'aah_configure_transaction_info_page');

function aah_get_transaction_info_by_tag($tag)
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
    tree.tag = %s';

    if (!($result = $wpdb->get_row($wpdb->prepare($sql, $tag), ARRAY_A))) {
        trigger_error("No matching transaction found.");
        return false;
    }

    return $result;

}