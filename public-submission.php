<?php
require_once 'header.php';
require_once 'image_function.php';
require_once 'library/Email.php';
$controller = new ControllerRealEstate();
$controllerAgent = new ControllerAgent();
$controllerPropertyType = new ControllerPropertyType();
$controllerPhoto = new ControllerPhoto();
$controllerCountry = new ControllerCountry();

//$agents = $controllerAgent->getAgents();
$propertytypes = $controllerPropertyType->getPropertyTypes();
$countries = $controllerCountry->getcountry("");
$extras = new Extras();

$ptype = 0;
if (!empty($_SERVER['QUERY_STRING'])) {
    if(isset($_GET['stype']) && $_GET['stype'] != ''){                
        $ptype = $_GET['stype'];
    }
}
if (isset($_POST) && !empty($_POST)) {
    $itm = new RealEstate();
    $desired_dir = Constants::IMAGE_UPLOAD_DIR;
	$itm->name = htmlspecialchars(trim(strip_tags($_POST['name'])), ENT_QUOTES);
	$itm->email = htmlspecialchars(trim(strip_tags($_POST['email'])), ENT_QUOTES);
    $itm->address = htmlspecialchars(trim(strip_tags($_POST['address'])), ENT_QUOTES);
    $itm->baths = htmlspecialchars(trim(strip_tags($_POST['baths'])), ENT_QUOTES);
    $itm->beds = isset($_POST['beds']) ? trim(strip_tags($_POST['beds'])) : 0;
    $itm->built_in = trim(strip_tags($_POST['built_in']));
    $itm->country = htmlspecialchars(trim(strip_tags($_POST['country'])), ENT_QUOTES);
    $itm->created_at = time();
    $itm->desc1 = '';
    $itm->featured = isset($_POST['featured']) ? htmlspecialchars(trim(strip_tags($_POST['featured'])), ENT_QUOTES) : 0;
    $itm->lat = trim(strip_tags($_POST['lat']));
    $itm->lon = trim(strip_tags($_POST['lon']));
    $itm->lot_size = trim(strip_tags($_POST['lot_size']));
    $itm->price = htmlspecialchars(trim(strip_tags($_POST['price'])), ENT_QUOTES);
    //$itm->price_per_sqft = htmlspecialchars(trim(strip_tags($_POST['price_per_sqft'])), ENT_QUOTES);
    //$itm->price_per_month = htmlspecialchars(trim(strip_tags($_POST['price_per_month'])), ENT_QUOTES);
    $itm->property_type = trim(strip_tags($_POST['property_type']));
    $itm->rooms = isset($_POST['rooms']) ? trim(strip_tags($_POST['rooms'])) : 0;
    $itm->sqft = trim(strip_tags($_POST['sqft']));
    $itm->status = trim(strip_tags($_POST['status']));
    $itm->updated_at = time();
    $itm->is_deleted = 0;
    $itm->agent_id = isset($_POST['agent_id']) ? trim(strip_tags($_POST['agent_id'])) : 0;
    //$itm->zipcode = trim(strip_tags($_POST['zipcode']));
    $itm->currency = trim(strip_tags($_POST['currency']));
    $itm->pname = trim(strip_tags($_POST['pname']));
    $itm->pdes = trim(strip_tags($_POST['pdes']));
    $itm->location = isset($_POST['location']) ? trim(strip_tags($_POST['location'])) : "";
    $itm->is_contact_price = isset($_POST['is_contact_price']) ? $_POST['is_contact_price'] : 0;
    $itm->telephone = htmlspecialchars(trim(strip_tags($_POST['telephone'])), ENT_QUOTES);
    
    $itemResult = $controller->insertRealEstate($itm);
    if ($itemResult) {
        $realestate_id = $controller->getLastInsertedId();
        if (!empty($realestate_id)) {
			
			// send email here 
			
			if(!empty($itm->email)) {
				$emailCls = new Email;
				
				$emailCls->sendEmail(
					"investmentjmvi@gmail.com",
					"Investment JMVI", 
					"Public property submission",
					"Please log in to the JMVI admin panel to check on this property for sale that a potential client has submitted.<br/><br/>
					Thanks,
					JMVI Team"
				);
				
				$emailCls->sendEmail(
					$itm->email,
					$itm->name, 
					"You have successfully submitted your property for sale to JMVI Realty",
					"Hi {$itm->name},<br/><br/>
					You have successfully submitted your property for sale to JMVI Realty with the following information:<br/><br/>
					Name of Property:{$itm->pname}<br/>
					Description:{$itm->pdes}<br/>
					Address:{$itm->address}<br/>
					Country:{$itm->country}<br/>
					Price:{$itm->price}<br/>
					Bedroom:{$itm->beds}<br/>
					Bathroom:{$itm->baths}<br/>
					Rooms:{$itm->rooms}<br/>
					Property Size (Sq. Ft):{$itm->sqft}<br/>
					Lot Size (Sq. Ft.):{$itm->lot_size}<br/>
					Built In:{$itm->built_in}<br/>
					Currency:{$itm->currency}<br/>
					Latitude:{$itm->lat}<br/>
					Longitude:{$itm->lon}<br/><br/>
					Thanks,
					JMVI Team"
				);
			}
			
            if (isset($_FILES['img_file']) && !empty($_FILES['img_file']) && !empty($_FILES['img_file']['name'][0])) {

                $count = count($_FILES["img_file"]["name"]);
                for ($key = 0; $key < $count; $key++) {

                    $file_name = $_FILES['img_file']['name'][$key];
                    $file_size = $_FILES['img_file']['size'][$key];
                    $file_tmp = $_FILES['img_file']['tmp_name'][$key];
                    $file_type = $_FILES['img_file']['type'][$key];

                    $timestamp = time();
                    $temp = explode(".", $file_name);
                    $extension = end($temp);
                    
                    $new_file_name = "property_".$key . $timestamp . "." . $extension;
                    $thumb_new_file_name = "thumb_property_".$key . $timestamp . "." . $extension;
                    if (is_dir($desired_dir) == false) {
                        // Create directory if it does not exist
                        mkdir("$desired_dir", 0700);
                    }

                    move_uploaded_file($file_tmp, $desired_dir . "/" . $new_file_name);
                    createThumbnail($desired_dir . "/" . $new_file_name, $desired_dir . "/" . $thumb_new_file_name, 400, 300, array(255,255,255));

                    $pitm = new Photo();
                    $pitm->photo_url = Constants::ROOT_URL . $desired_dir . '/' . $new_file_name;
                    $pitm->thumb_url = Constants::ROOT_URL . $desired_dir . '/' . $thumb_new_file_name;
                    $pitm->realestate_id = $realestate_id;
                    $pitm->created_at = time();
                    $pitm->updated_at = time();
                    $phRes = $controllerPhoto->insertPhoto($pitm);
                }
            }
        }
    }
    $response = [
        'status' => '200',
        'message' => 'Property Added Successfully'
    ];
    echo json_encode($response);
    exit;
}
?>


<!DOCTYPE html>
<html lang="en"><head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="bootstrap/images/16px_JMVI_logo.png">

        <title>JMVI Real Estate</title>

        <!-- Bootstrap core CSS -->
        <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
        <!-- Custom styles for this template -->
        <link href="bootstrap/css/navbar-fixed-top.css" rel="stylesheet">
        <link href="bootstrap/css/custom.css" rel="stylesheet">
        <link href="bootstrap/css/fileinput.css" rel="stylesheet">
		<style>
		form.form_add label.error, label.error {
			color: red;
			font-style: italic;
		}
		</style>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_API_KEY; ?>&sensor=false"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script type="text/javascript">
            $(function () {
                var mapDiv = document.getElementById('map');
                var map = new google.maps.Map(mapDiv, {
                    center: new google.maps.LatLng(<?php echo Constants::MAP_DEFAULT_LATITUDE . "," . Constants::MAP_DEFAULT_LONGITUDE; ?>),
                    zoom: <?php echo Constants::MAP_DEFAULT_ZOOM_LEVEL_ADD; ?>,
                    mapTypeId: "satellite",
                });

                var marker;
                google.maps.event.addListener(map, 'click', function (mouseEvent) {

                    if (marker != null)
                        marker.setMap(null);

                    var lat = document.getElementById('latitude');
                    var longi = document.getElementById('longitude');
                    lat.value = mouseEvent.latLng.lat(); //alert(mouseEvent.latLng.toUrlValue());
                    longi.value = mouseEvent.latLng.lng();

                    marker = new google.maps.Marker({
                        position: mouseEvent.latLng,
                        map: map,
                        title: 'Here!'
                    });

                });

            });

            function validateLatLng(evt) {
                var theEvent = evt || window.event;
                var key = theEvent.keyCode || theEvent.which;
                key = String.fromCharCode(key);

                if (theEvent.keyCode == 8 || theEvent.keyCode == 127) {

                } else {
                    var regex = /[0-9.]|\./;
                    if (!regex.test(key)) {
                        theEvent.returnValue = false;
                        if (theEvent.preventDefault)
                            theEvent.preventDefault();
                    }
                }
            }
        </script>


        <!-- Just for debugging purposes. Don't actually copy this line! -->
        <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body style="padding-top:0px;">
		<div class="carousel-item active" style="background-image: url('images/public_submission_banner.png');background-size: 100% 100%;height: 500px;text-align: center;line-height: 500px;">
		  <img  style="width:200px;height:200px;margin: 0px auto;" src="images/logo.png" alt="">
		</div>
		<div class="container">
			<div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <form action="javascript:void(0);" method="POST" id="public-approval-property" class="form_add">
								<div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Name" id="name" name="name" required>                             
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Telephone Number</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Telephone Number" id="telephone" name="telephone" required>                             
                                    </div>
                                </div>
								<div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" placeholder="Email" id="email" name="email" required>                             
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Name of Property</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Name of Property" id="pname" name="pname" required>                             
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Description</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" placeholder="Property Description" id="pdes" name="pdes" required></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Address</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Address" id="address" name="address" required>
                                    </div>
                                </div>
								<?php /*
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Location</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Location" required name="location">
                                    </div>
                                </div>
								*/ 
                                ?>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Country</label>
                                    <div class="col-sm-10">
										<select class="form-control" style="width:100%;" id="country" required name="country">
                                            <?php
                                            if ($countries != null) {
                                                foreach ($countries as $country) {
                                                    echo "<option value='$country->country_name'>$country->country_name</option>";
                                                }
                                            }
                                            ?>

                                        </select>
                                    </div>
                                </div>
                                <?php /*
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Postal Code</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Postal Code" required name="zipcode">
                                    </div>
                                </div> 
                                  */ 
                                ?>                   
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Property Type</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" style="width:100%;" id="property_type" name="property_type">
                                            <option value="None">Select Property Type</option>
                                            <?php
                                            if ($propertytypes != null) {
                                                foreach ($propertytypes as $propertytype) {
                                                    echo "<option value='$propertytype->propertytype_id'>$propertytype->property_type</option>";
                                                }
                                            }
                                            ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Price:</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Price" id="price" name="price" required>
                                    </div>
                                </div>
                                <?php /*
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Property Value</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Price/Sqft" required name="price_per_sqft">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Price/Month</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Price/Month" name="price_per_month">
                                    </div>
                                </div>                                
                                */ ?>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Bedrooms<br/>(if none type  "0")</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Beds" required name="beds" id="beds">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Bathrooms<br/>(if none type "0")</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Baths" name="baths" id="baths" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Rooms<br/>(if none type "0")</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Rooms" required id="rooms" name="rooms">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Property Size (Sq. Ft)</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Property Size (Sq. Ft)" required id="sqft" name="sqft">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Lot Size (Sq. Ft.)</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Lot Size (Sq. Ft.)" required id="lot_size" name="lot_size">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Built In</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Built In" required name="built_in" id="built_in">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Currency</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Currency" name="currency" required id="currency">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mortgage_dep_number" class="col-sm-2 col-form-label">Photo Gallery<br/>(Max. 10 photos 1mb or less)</label>
                                    <input type="hidden" name="property_img" id="property_img">
                                    <div class="col-sm-10">
                                        <div class="main-image-preview_product">
                                            <div class="photo_preview_div">

                                            </div>
                                            <div class="col-sm-12 row">
                                                <div class="file-loading">
                                                    <input id="input-fa" name="imgs_file[]" type="file" multiple class="deal_image_cropper"  rel ='product'>
                                                </div>                             
                                            </div>
                                        </div>                                        
                                    </div>
                                </div> 
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Site Location</label>
                                    <div class="col-sm-10">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <h4>Click the Map to get latitude/longitude:</h4>
                                                <div id="map" style="width:100%; height:400px"></div>
                                            </div>
                                            <div class="col-sm-4">
                                                <span style="margin-top:200px; display: block;"><i class="fa fa-info-circle" aria-hidden="true"></i> Zoom in to locate property</span>
                                            </div>
                                        </div>                                        
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Latitude</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Click on the Map for Latitude" name="lat" onkeypress='validateLatLng(event)' id="latitude" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Longitude</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Click on the Map for Longitude" name="lon" onkeypress='validateLatLng(event)' id="longitude" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Select Agent</label>
                                    <div class="col-sm-10">
                                        <select name="agent_id" class="form-control">
                                            <option value="40">Public Listing</option>
                                        </select>
                                    </div>
                                </div>
								<input type="hidden" name="status" value="4">
                                <div class="form-group form-check">
									<input type="checkbox" name="agree" class="form-check-input" id="agree-box" required>
									<label class="form-check-label" for="agree-box">I agree to JMVI terms and conditions and I own this property and/or are authorised to offer it on the market for sale.</label>
								</div>		
                                <p>
                                    <button type="submit" name="submit" class="btn btn-info" role="button">Save</button> 
                                    <a class="btn btn-info" href="realestates.php" role="button">Cancel</a>
                                </p>
                            </form> 
                        </div>

                    </div>
                </div>


            </div> <!-- /container -->


            <!-- Bootstrap core JavaScript
            ================================================== -->
            <!-- Placed at the end of the document so the pages load faster -->
            <?php require_once 'footer_js.php'; ?>
            <script src="bootstrap/js/fileinput.js"></script>
            <script src="bootstrap/js/theme.js"></script>
            <script src="js/jquery.validate.min.js"></script>

            <script>
                                        $("#public-approval-property").validate({
											submitHandler: function() {
												checkInput();
											}
										});
										var imgFiles = [];                                        
                                        $("#input-fa").fileinput({
                                            uploadUrl: "/realestate_insert.php",
                                            theme: "fa",
                                            enableResumableUpload: true,
                                            allowedFileExtensions: ['jpg', 'png', 'jpeg'],
                                            maxFileSize:1000,
                                            showPreview: true,
                                            showRemove: true,
                                            showUpload: false,
                                            showCaption: false,
                                            browseOnZoneClick: true,
                                            showBrowse: false,
                                            overwriteInitial: false,
                                            msgSizeTooLarge: 'File "{name}" (<b>{size} KB</b>) exceeds maximum allowed upload size of <b>1 MB</b>.'
                                        }).on('fileloaded', function (event, file, previewId, index, reader) {
                                            imgFiles.push(file);
                                        });

                                        function checkInput() {
                                            var formData = new FormData($('.form_add')[0]);
                                            $.each(imgFiles, function (i, file) {
                                                formData.append('img_file[]', file);
                                            });

                                            $('form.form_add').find('input').each(function () {
                                                if ($(this).attr('required') && $(this).val().trim() == '') {
                                                    return false;
                                                }
                                            });

                                            $.ajax({
                                                type: 'POST',
                                                url: 'public-submission.php',
                                                cache: false,
                                                contentType: false,
                                                processData: false,
                                                data: formData,
                                                //dataType : 'text',
                                                dataType: 'json',
                                                success: function (data)
                                                {
													alert("You have successfully submitted your property for sale to JMVI Realty.Please await approval from one of our team members.");
													location.reload();
                                                    //window.location.href = 'realestates.php?stype=<?= $ptype ?>';
                                                }
                                            });
                                            return false;


                                        }
            </script>
    </body>
</html>