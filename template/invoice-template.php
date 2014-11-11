<?php

if (isset($_POST['Submit'])) {

    if (!isset($logo_src)) { $logo_src = ''; }
    if (!isset($address_to)) { $address_to = ''; }
    if (!isset($address_from)) { $address_from = ''; }
    if (!isset($date)) { $date = ''; }
    if (!isset($invoice_nr)) { $invoice_nr = ''; }
    if (!isset($invoice_total_due)) { $invoice_total_due = 0; }
    if (!isset($item_name)) { $item_name = ''; }
    if (!isset($invoice_subtotal)) { $invoice_subtotal = 0; }
    if (!isset($invoice_total_paid)) { $invoice_total_paid = 0; }
    if (!isset($notes)) { $notes = ''; }

    $html = '
    <html>
        <link rel="stylesheet" type="text/css" href="template/invoice-style.css">
        <body style="background-image:url(assets/img/bg-pdf.png);background-image-resize:1;">
            <div style="padding-bottom:120px;"></div>
            <div class="bill-to">';
            
    if ($logo_src != '') {
        $html .= '
                <img src="'.$logo_src.'" class="invoice-logo"><br><br>';
    }

    $html .= '
                <h3>Bill to:</h3>
                <p>'.$address_to.'</p>
            </div>
            
            <div class="invoice-head">
                <div class="bill-from">
                    <p>'.$address_from.'</p>
                </div>
                
                <h1>Invoice</h1>
              
                <table cellpadding="5" cellspacing="5" width="100%">';
                
    if ($date != '') {
        $html .='
                    <tr>
                        <th>Date</th>
                        <td>'.$date.'</td>
                    </tr>';
    }
              
    if ($invoice_nr != '') {
        $html .= '
                    <tr>
                        <th>Invoice #</th>
                        <td>'.$invoice_nr.'</td>
                    </tr>';
    }
              
    $html .= '
                    <tr>
                        <th>Amount due</th>
                        <td>$ '.number_format($invoice_subtotal,2, '.', ',').'</td>
                    </tr>
                </table>
            </div>';
       
    $html .= '
            <div class="invoice-items">
                <br><br>
                <table cellpadding="5" cellspacing="5" width="100%">
                   <thead>
                       <tr>
                           <th>Item</th>
                           <th>Description</th>
                           <th>Price</th>
                           <th>Qty</th>
                           <th width="20%">Total</th>
                       </tr>
                   </thead>';
       
    // output each invoice line of items
    if (!empty($item_name)) {
    foreach($item_name as $a => $b) {
    $html .= '
                    <tr>
                        <td>'.makeSafe($item_name[$a]).'</td>
                        <td>'.makeSafe($item_desc[$a]).'</td>
                        <td>$ '.makeSafe(number_format(floatval($item_price[$a]),2, '.', ',')).'</td>
                        <td>'.makeSafe($item_qty[$a]).'</td>
                        <td align="right">$ '.makeSafe(number_format($item_qty[$a] * $item_price[$a],2, '.', ',')).'</td>
                    </tr>';
    }
    }
    $html .= '
                    <tr>
                        <td colspan="4" align="right">Total</td>
                        <td align="right"><strong>$ '.number_format($invoice_subtotal,2, '.', ',').'</strong></td>
                    </tr>
                </table>
            </div>
            <br><br>
            <div class="invoice-notes"><p>'.$notes.'</p></div>
        </body>
    </html>'; 

}
?>