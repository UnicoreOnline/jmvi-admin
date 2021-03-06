<?php
require_once 'header.php';
$controller = new ControllerReservedProperty();

$searchPrams = [];
$reservedProperty = [];
if (!empty($_SERVER['QUERY_STRING'])) {

    
    $extras = new Extras();
    $id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);
    
    if (isset($id) && $id > 0) {
        $reservedProperty = $controller->getReservedPropertyById($id);
    }

}


// Include the main TCPDF library (search for installation path).
require_once('tcpdf/tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('JMVI');
$pdf->SetTitle('JMVI Invoice');
$pdf->SetSubject('JMVI Invoice');
$pdf->SetKeywords('JMVI Invoice');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// set default monospaced font
//$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('helvetica', '', 12, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage('P', 'A4');

// set text shadow effect
//$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print
$html = <<<EOD
<style>    
* { margin: 0; padding: 0; }
body { font: 14px/1.4 Georgia, serif; }
#page-wrap { width: 800px; margin: 0 auto; }

textarea { border: 0; font: 14px Georgia, Serif; overflow: hidden; resize: none; }
table { border-collapse: collapse; }
table td, table th { border: 1px solid black; padding: 5px; }

#header { height: 15px; width: 100%; margin: 20px 0; background: #222; text-align: center; color: white; font: bold 15px Helvetica, Sans-Serif; text-decoration: uppercase; letter-spacing: 20px; padding: 8px 0px; }

#address { width: 250px; height: 150px; float: left; }
#customer { overflow: hidden; }

#logo { text-align: right; float: right; position: relative; margin-top: 10px; border: 1px solid #fff; width: 50%; overflow: hidden; }
/* #logo:hover, #logo.edit { border: 1px solid #000; margin-top: 0px; max-height: 125px; } */
#logoctr { display: none; }
/* #logo:hover #logoctr, #logo.edit #logoctr { display: block; text-align: right; line-height: 25px; background: #eee; padding: 0 5px; } */
#logohelp { text-align: left; display: none; font-style: italic; padding: 10px 5px;}
#logohelp input { margin-bottom: 5px; }
.edit #logohelp { display: block; }
.edit #save-logo, .edit #cancel-logo { display: inline; }
.edit #image, #save-logo, #cancel-logo, .edit #change-logo, .edit #delete-logo { display: none; }
#customer-title { font-size: 20px; font-weight: bold; float: left; }

#meta { margin-top: 1px; width: 300px; float: right; }
#meta td { text-align: right;  }
#meta td.meta-head { text-align: left; background: #eee; }
#meta td textarea { width: 100%; height: 20px; text-align: right; }

#items { clear: both; width: 100%; margin: 30px 0 0 0; border: 1px solid black; }
#items th { background: #eee; }
#items textarea { width: 80px; height: 50px; }
#items tr.item-row td { border: 0; vertical-align: top; }
#items td.description { width: 300px; }
#items td.item-name { width: 175px; }
#items td.description textarea, #items td.item-name textarea { width: 100%; }
#items td.total-line { border-right: 0; text-align: right; }
#items td.total-value { border-left: 0; padding: 10px; }
#items td.total-value textarea { height: 20px; background: none; }
#items td.balance { background: #eee; }
#items td.blank { border: 0; }

#terms { text-align: center; margin: 20px 0 0 0; }
#terms h5 { text-transform: uppercase; font: 13px Helvetica, Sans-Serif; letter-spacing: 10px; border-bottom: 1px solid black; padding: 0 0 8px 0; margin: 0 0 8px 0; }
#terms textarea { width: 100%; text-align: center;}

textarea:hover, textarea:focus, #items td.total-value textarea:hover, #items td.total-value textarea:focus, .delete:hover { background-color:#EEFF88; }

.delete-wpr { position: relative; }
.delete { display: block; color: #000; text-decoration: none; position: absolute; background: #EEEEEE; font-weight: bold; padding: 0px 3px; border: 1px solid; top: -6px; left: -22px; font-family: Verdana; font-size: 12px; }

</style>
<div id="page-wrap">
		<p id="header">INVOICE</p>
		<div id="identity">
			<p id="address">
				Howard Britton<br>
				123 Appleseed Street<br>
				Appleville, WI 53719<br>
				Phone: (555) 555-5555<br>
			</p>
            <div id="logo">
		<img src='https://www.jmviapp.com/jmvi_new/images/jmvi_logo.png' width="100" height="auto">
            </div>
		</div>
		<div style="clear:both"></div>
		<div id="customer">
			<p>
				Customer Name: John Brown<br>
				Customer Addess: 12 Florida Street<br>
				Customer Phone Number: 1(xxx)xxx-xxxx
			</p>
            <table id="meta">
                <tr>
                    <td class="meta-head">Invoice #</td>
                    <td><p>000123</p></td>
                </tr>
                <tr>
                    <td class="meta-head">Date</td>
                    <td><p id="date">December 15, 2009</p></td>
                </tr>
                <tr>
                    <td class="meta-head">Amount Due</td>
                    <td><div class="due">$875.00</div></td>
                </tr>
            </table>
		</div>
		<table id="items">
		  <tr>
		      <th>Property Name</th>
		      <th>Address</th>
		      <th>Quantity</th>
		      <th>Currency</th>
		      <th>Price</th>
		  </tr>
		  <tr class="item-row">
		      <td class="item-name"><div class="delete-wpr"><p>Cherry Flat</p></div></td>
		      <td class="description"><p>Mckinnons</p></td>
		      <td><p class="cost">1</p></td>
		      <td><p class="currency">ECD</p></td>
		      <td><span class="price">$650.00</span></td>
		  </tr>
		  <tr class="item-row">
		      <td class="item-name"><div class="delete-wpr"><p>Sea View Apartments</p></div></td>

		      <td class="description"><p>Sea View Farm</p></td>
		      <td><p class="qty">1</p></td>
		      <td><p class="currency">ECD</p></td>
		      <td><span class="price">$225.00</span></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line">Subtotal</td>
		      <td class="total-value"><div id="subtotal">$875.00</div></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line">Total</td>
		      <td class="total-value"><div id="total">$875.00</div></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line">Amount Paid</td>

		      <td class="total-value"><p id="paid">$0.00</p></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line balance">Balance Due</td>
		      <td class="total-value balance"><div class="due">$875.00</div></td>
		  </tr>
		</table>
		<div id="terms">
		  <h5>Terms</h5>
		  <p>NET 90 Days. Finance Charge of 1.5% will be made on unpaid balances after 90 days.</p>
		</div>
	</div>
EOD;
//echo $html;exit;
// Print text using writeHTMLCell()


$pdf->writeHTML($html, true, false, true, false, '');
// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('example_001.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
