<?php

// todo transaction lookup
function aah_render_transaction_lookup()
{
    ?>
    <h2>Search Transactions</h2><br>
    <p>You can search by adoption ID or tree tag.</p>
    <div class="transaction-lookup">
        <form action="" method="post" class="transaction-lookup-form">
            <div class="form-field">
                <input name="search-key" type="text">
                <label>Tree Tag <input type="radio" id="tag-search-type" name="search-type" value="tree-tag"></label>
                <label>Transaction ID <input type="radio" id="id-search-type" name="search-type" value="transaction-id"></label>
            </div>
        </form>
        <button type="button" class="transaction-lookup-button">Search</button>
    </div>

    <?php

    // if the search was submitted
    if (isset($_POST['search-key']) and $_POST['search-type']) {

        ?>


        <?php
    }
}

// This adds the menu page for transaction management
function aah_configure_transaction_info_page()
{
    add_menu_page("Adopt-a-Hemlock Transaction Management", "Adopt-a-Hemlock Transaction Management",
        "manage_options", "adopt-a-hemlock-transaction-info", "aah_render_transaction_lookup");
}

// hook to add settings to admin menu
add_action('admin_menu', 'aah_configure_transaction_info_page');
