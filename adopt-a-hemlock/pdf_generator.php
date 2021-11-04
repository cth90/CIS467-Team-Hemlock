<?php
//require_once("TCPDF/tcpdf_autoconfig.php");
require_once("TCPDF/tcpdf.php");
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
    $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->AddPage();

    $info = aah_get_transaction_info($transaction_id);

    // todo write pdf
    $pdf->Image(CERT_URL);

    $file_path = get_home_path() . "wp-content/pdfs/" . $transaction_id . ".pdf";
    $pdf->Output($file_path, "F");
    $url = content_url() . "/pdfs/" . $transaction_id . ".pdf";
    return $url;
}
//sql query for each element of the array
function aah_get_transaction_info($transaction_id) {
    $info = array(
        0=>'', //name
        1=>'', //date
        2=>'', //tree_tag
        3=>'', //location_name
        5=>'', //longitude
        6=>'', //latitude
    );

    return $info;
}
