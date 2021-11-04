<?php

require_once("TCPDF/tcpdf.php");
define('CERT_URL', plugin_dir_url(__FILE__) . 'certificate.png');

// Extend the TCPDF class to create custom Header and Footer
class CERTIFICATE extends TCPDF {
    //Page header
    public function Header() {
        // get the current page break margin
       //$bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        $this->SetAutoPageBreak(false, 0);
        // set bacground image
        $this->Image(CERT_URL, 0, 0, 0, 0, '', '', '', true, 300, 'C', false, false, 0, false, false, true);
        // restore auto-page-break status
        //$this->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $this->setPageMark();
    }
}