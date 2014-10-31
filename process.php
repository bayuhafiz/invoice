<?php error_reporting(E_ALL);
/**
* InvoiceForm - Editable invoice generator
* @author Adriaan Ebbeling
* @version 1.0
*/

// converts text to safe output (prevents cross-site-scripting)
function makeSafe($text) 
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// converts linebreaks from textareas to <BR> tags
function lineBreaks($text) 
{ 
    return strtr($text, array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />')); 
} 

// generates invoice in PDF format
function generateInvoice($address_from, $address_to, $logo_src, $date, $invoice_nr, $notes, $item_name, $item_qty, $item_desc, $item_price, $invoice_total_paid, $invoice_subtotal, $invoice_taxrate, $invoice_total_tax, $invoice_total_due, $action) 
{

    // include php invoice templates
    include_once 'template/invoice-template.php';

    if ($invoice_nr != '') {
        $invoice_filename = $invoice_nr . '-invoice.pdf';
    } else {
        $invoice_filename = 'invoice.pdf';
    }

    // include mpdf library
    include_once 'resources/libraries/MPDF57/mpdf.php';
    
    // create new mPDF
    $mpdf = new mPDF(); 
    
    //$mpdf->setFooter('{PAGENO}');
    $mpdf->WriteHTML($html);
    
    if ($action == "view") {
        $mpdf->Output($invoice_filename, 'i');
        unlink('uploads/logos/'.$logo_src.'');
    } else {
        $mpdf->Output($invoice_filename, 'd');
        unlink('uploads/logos/'.$logo_src.'');
    }
    
    exit;  
}
  
// invoice form has been submitted
if (isset ($_POST['Submit'])) {
  
    $upload_dir = 'uploads/logos/';
    $random = rand(00000, 99999) . '_';
    
    // Check if logo filetype is GIF, JPG or PNG and upload logo
    $ext = pathinfo($_FILES['invoice_logo']['name'], PATHINFO_EXTENSION);
    $allowed = array('jpg','png','gif');
    if (!in_array($ext, $allowed)) {
        $logo_src = '';
    } else {
        if(move_uploaded_file($_FILES['invoice_logo']['tmp_name'], $upload_dir . $random . $_FILES['invoice_logo']['name'])) {
            $logo_src = $upload_dir . $random .  $_FILES['invoice_logo']['name'];
        } else {  
            $logo_src = '';
        }
    }
    
    // save all data input as variable and use makeSafe to prevent xss
    $address_from       = linebreaks(makeSafe($_POST['invoice_address_from']));
    $address_to         = linebreaks(makeSafe($_POST['invoice_address_to']));
    $logo               = $_FILES['invoice_logo']['name'];
    $date               = makeSafe($_POST['invoice_date']);
    $invoice_nr         = makeSafe($_POST['invoice_nr']);
    $notes              = linebreaks(makeSafe($_POST['invoice_note']));
    $item_name          = $_POST['item_name'];
    $item_qty           = $_POST['item_qty'];
    $item_price         = $_POST['item_price'];
    $item_desc          = $_POST['item_description'];
    $invoice_total_paid = makeSafe($_POST['invoice_total_paid']);
    $invoice_taxrate    = makeSafe($_POST['invoice_taxrate']);
    $invoice_total_tax  = makeSafe($_POST['invoice_total_tax']);
    $form_action        = makeSafe($_POST['Submit']);
    
    if ($invoice_total_paid == '') {
        $invoice_total_paid = 0;
    }
    
    $invoice_subtotal = 0;
    
    // account item prices and add to subtotal
    foreach($item_price as $a => $b) {
        $invoice_subtotal = $invoice_subtotal + ($item_price[$a] * $item_qty[$a]);
    }
    
    $invoice_total_due = $invoice_subtotal + $invoice_total_tax - $invoice_total_paid;
    $invoice_taxrate = $invoice_taxrate * 100 - 100;

    // Create the invoice
    generateInvoice($address_from, $address_to, $logo_src, $date, $invoice_nr, $notes, $item_name, $item_qty, $item_desc, $item_price, $invoice_total_paid, $invoice_subtotal, $invoice_taxrate, $invoice_total_tax, $invoice_total_due, $form_action); 
}
?>