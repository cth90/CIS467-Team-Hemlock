<?php
require_once 'adoption_page.php';

// todo transaction lookup
function aah_render_transaction_lookup()
{
    ?>
    <h2>Search Transactions</h2><br>
    <p>You can search by adoption ID or tree tag.</p>
    <div class="transaction-lookup">
        <form action="" method="post" class="transaction-lookup-form">
            <div class="form-field">
                <input name="search-key" type="text"><br>
                <label>Tree Tag <input type="radio" id="tag-search-type" name="search-type" value="tree-tag"></label>
                <label>Adoption ID <input type="radio" id="id-search-type" name="search-type" value="aid"></label>
                <button type="submit" class="transaction-lookup-button">Search</button>
            </div>
        </form>
    </div><br><br>

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
            if (!empty($_POST['tree_tag']) AND $_POST['tree_tag'] != $transaction['tree_tag']) {
                if (!($edited_transaction['tree_id'] = aah_get_unadopted_tree_by_tag($_POST['tree_tag']))) {
                    echo "<h2>Reassigned Tree Tag Not Found!</h2>";
                }
            }
            if (!empty($_POST['name']) AND $_POST['name'] != $transaction['name']) {
                $edited_transaction['name'] = $_POST['name'];
            }
            if (!empty($_POST['email']) AND $_POST['email'] != $transaction['email']) {
                $edited_transaction['email'] = $_POST['email'];
            }
            if (!empty($_POST['anonymous'])) {
                $edited_transaction['anonymous'] = $_POST['anonymous'];
            }

            if(!(aah_edit_transaction($transaction_id, $edited_transaction))) {
                echo "<br><h2>Transaction Edit Failed!</h2>";
            }
        }

        if ($transaction) {
            ?>
                <br><br>
            <div class="transaction-info-form">
                <form action="" method="post" class="transaction-edit-form">
                    <label>Adoption ID: <a href="<?php echo site_url('adoption-information') . "?aid=" . $transaction['a_id']; ?>"><?php echo $transaction['a_id']; ?></a></label>
                    <label>Tree Tag: <input name="tree_tag" id="tree_tag"
                                            value="<?php echo $transaction['tree_tag']; ?>"></label>
                    <label>Name: <input name="name" id="name" value="<?php echo $transaction['name']; ?>"></label>
                    <label>Email: <input name="email" id="email" value="<?php echo $transaction['email']; ?>"></label>
                    <label>Anonymous: <input type="checkbox" name="anonymous" id="anonymous" value="1" <?php
                        if ($transaction['anonymous']) {
                            echo "checked";
                        }
                        ?>></label>
                    <input type="hidden" name="transaction_id" id="transaction_id"
                           value="<?php echo $transaction['transaction_id']; ?>">
                    <input type="hidden" name="search-key" id="search-key" value="<?php echo $_POST['search-key']; ?>">
                    <input type="hidden" name="search-type" id="search-type" value="<?php echo $_POST['search-type']; ?>">
                    <input type="hidden" name="edited" id="edited" value="1">
                    <button type="submit" class="transaction-edit-button">Submit Edits</button>
                </form>
            </div>

            <?php
        }
    }
}

// edit a given transaction
function aah_edit_transaction($transaction_id, array $edited_transaction)
{
    global $wpdb;

    $edited_transaction['pdf_link'] = "";

    // update transaction
    if(!($wpdb->update("aah_transactions", $edited_transaction, array("id"=>$transaction_id)))) {
        trigger_error("Unable to edit transaction.");
        return false;
    }
    return true;
}

// This adds the menu page for transaction management
function aah_configure_transaction_info_page()
{
    add_menu_page("Adopt-a-Hemlock Transaction Management", "Adopt-a-Hemlock Transaction Management",
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