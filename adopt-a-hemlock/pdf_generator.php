<?php
//require_once("TCPDF/tcpdf_autoconfig.php");
require_once('CERTIFICATE.php');
define('CERT_URL', plugin_dir_url(__FILE__) . 'certificate.png');

function aah_render_pdf_generator()
{
    ob_start();
    ?>
    <div class="pdf-generator">
        <form action="" method="post" class="pdf-generator-form">
            <div class="form-field">
                <label>Transaction ID: </label>
                <input name="transaction_id" type="text">
            </div>
            <input type="hidden" name="action" value="aah_get_pdf_by_transaction">
        </form>
        <button type="button" class="pdf-get-button">Get PDF</button>
    </div>
    <div class="pdf-url">
    </div>
    <script type="text/javascript">
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        jQuery(document).ready(function ($) {
            $('.pdf-get-button').click(function () {
                var form_data = jQuery('.pdf-generator-form').serializeArray();
                $.post(ajaxurl, form_data, function (response) {
                    $('.pdf-url').html('<a href="' + response['url'] + '">PDF</a>');
                });
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('pdf_generator', 'aah_render_pdf_generator');

function aah_get_pdf_by_transaction_ajax() {
    $result['url'] = "";
    if(isset($_POST['transaction_id'])) {
        $result['url'] = aah_get_pdf_by_transaction(intval($_POST['transaction_id']));
    }
    wp_send_json($result);
}
add_action('wp_ajax_aah_get_pdf_by_transaction', 'aah_get_pdf_by_transaction_ajax');
add_action('wp_ajax_nopriv_aah_get_pdf_by_transaction', 'aah_get_pdf_by_transaction_ajax');

// Takes a transaction id and returns a pdf as a string url.
function aah_get_pdf_by_transaction($transaction_id) {
    $pdf = new CERTIFICATE('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->AddPage();

    $info = aah_get_transaction_info($transaction_id);

    // Write name
    $pdf->SetY(110);
    $pdf->write(5, $info['name'], null, false, 'C');
    // Write tree location name
    $pdf->SetXY(135,136);
    $pdf->write(5, $info['location_name']);
    // Write tree tag
    $pdf->SetXY(135,141);
    $pdf->write(5, $info['tree_tag']);
    // Write tree coords
    $pdf->SetXY(135,145);
    $pdf->write(5, $info['latitude'] . ", " . $info['longitude']);
    // Write date
    $pdf->SetXY(70, 170);
    $pdf->write(5, $info['date']);

    // todo write notes


    $pdfs_path = get_home_path() . "wp-content/pdfs/";
    $file_name =  $transaction_id . ".pdf";
    // Create pdfs directory if it doesn't exist
    if (!file_exists($pdfs_path)) {
        mkdir( $pdfs_path,0755,false );
    }

    $pdf->Output($pdfs_path . $file_name, "F");
    $url = content_url() . "/pdfs/" . $file_name;
    return $url;
}
//sql query for each element of the array
function aah_get_transaction_info($transaction_id): array
{
    global $wpdb;

    $sql = 'SELECT
	tree.`name` AS name,
	tree.`completed` AS date,
	t.`tag` as tree_tag,
	l.`name` as location_name,
	l.`address` as location_address,
	t.`longitude` as longtitude,
	t.`latitude` as latitude,
	tree.`amt_donated` as donation_amt
FROM
    `aah_transactions` as tree
inner join `aah_trees` as t on t.tag = tree.tree_id
inner join `aah_locations` as l on l.id = t.location_id';

    $info = array(
        'name'=>'Placeholder Name',
        'date'=>date('m/d/Y'),
        'tree_tag'=>'0000',
        'location_name'=>'Wherever',
        'longitude'=>'9999999.9999',
        'latitude'=>'9999999.9999',
        'notes'=>''
    );

    return $info;
}
