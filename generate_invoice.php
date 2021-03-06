<?php
//set_include_path(get_include_path() . PATH_SEPARATOR . "/path/to/dompdf");
require_once 'header.php';

$controller = new ControllerReservedProperty();

$searchPrams = [];
$reservedProperty = [];
$desired_dir = Constants::IMAGE_UPLOAD_DIR;
$extras = new Extras();
if (!empty($_SERVER['QUERY_STRING'])) {
    
    $id = $extras->decryptQuery1(KEY_SALT, $_SERVER['QUERY_STRING']);
    
    if (isset($id) && $id > 0) {
        $reservedProperty = $controller->getReservedPropertyById($id);        
    }

}
$username = isset($reservedProperty->user_name) ? $reservedProperty->user_name : '';
$propertyname = isset($reservedProperty->property_name) ? $reservedProperty->property_name : '';
$useraddress = isset($reservedProperty->user_address) ? $reservedProperty->user_address : '';
$propertyaddress = isset($reservedProperty->propery_address) ? $reservedProperty->propery_address : '';
$mobile = isset($reservedProperty->mobile) && $reservedProperty->mobile != 0 ? $reservedProperty->mobile : '';
$todayDate = date('F d, Y');
$invoiceCode = '00'.$reservedProperty->id;
$price = isset($reservedProperty->price) ? $reservedProperty->price : '';
$currency = isset($reservedProperty->currency) ? $reservedProperty->currency : '';
$pdes = isset($reservedProperty->pdes) ? $reservedProperty->pdes : '';

require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();

$html = <<<EOD
<style>    
@font-face {
  font-family: 'Georgia';
  font-style: normal;
  font-weight: normal;
  src: url('https://proposalways.com/font/Georgia/Georgia.ttf') format('truetype');
}
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

#meta { margin-top: 0px; width: 300px; text-align: right;right:0; clear:both;}
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
				JMVI Realty<br>
				Deanery Place,<br>
				Dickenson Bay Street,<br>
				St. Johns, Antigua<br>
				P.O.Box 2372<br>
			</p>
            <div id="logo">
		<img src='/var/www/html/project_new/images/jmvi_pdf_logo.png' width="100" height="auto">
            </div>
		</div>
		<div style="clear:both"></div>
		<div id="customer">
			<p>
				Customer Name: {$username}<br>
				Customer Address: {$useraddress}<br>
				Customer Phone Number: {$mobile}
			</p>
                        <table>
                            <tr>
                                <td width="485px;" style="border:0px;">&nbsp;</td>
                                <td width="300px" style="border:0px;">
					<table id="meta">
						<tbody><tr>
							<td class="meta-head">Invoice #</td>
							<td><p>{$invoiceCode}</p></td>
						</tr>
						<tr>
							<td class="meta-head">Date</td>
							<td><p id="date">{$todayDate}</p></td>
						</tr>
						<tr>
							<td class="meta-head">Amount Due</td>
							<td><div class="due">$ {$price}</div></td>
						</tr>
					</tbody></table>
                                </td>
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
		      <td class="item-name"><div class="delete-wpr"><p>{$propertyname}</p></div></td>
		      <td class="description"><p>{$propertyaddress}</p></td>
		      <td><p class="cost">1</p></td>
		      <td><p class="currency">{$currency}</p></td>
		      <td><span class="price">$ {$price}</span></td>
		  </tr>		  
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line">Subtotal</td>
		      <td class="total-value"><div id="subtotal">$ {$price}</div></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line">Total</td>
		      <td class="total-value"><div id="total">$ {$price}</div></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line">Amount Paid</td>

		      <td class="total-value"><p id="paid">$0.00</p></td>
		  </tr>
		  <tr>
		      <td colspan="2" class="blank"> </td>
		      <td colspan="2" class="total-line balance">Balance Due</td>
		      <td class="total-value balance"><div class="due">$ {$price}</div></td>
		  </tr>
		</table>
		<div id="terms">
		  <h5>Terms</h5>
		  <p>NET 90 Days. Finance Charge of 1.5% will be made on unpaid balances after 90 days.</p>
		</div>
	</div>
EOD;

//echo $html;exit;
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
//$dompdf->stream();
$output = $dompdf->output();

$filePath = $desired_dir.'/invoice/invoice_'.$invoiceCode.'.pdf';
file_put_contents($filePath, $output);

$reservedProperty->invoice = 'invoice_'.$invoiceCode.'.pdf';
$invoiceUpdate = $controller->updateInvoice($reservedProperty);        
if($invoiceUpdate){
    $invoiceUrl = $extras->encryptQuery1(KEY_SALT, 'id', $reservedProperty->id, 'invoice_view.php');
    header('Location:'.$invoiceUrl);
}
?>