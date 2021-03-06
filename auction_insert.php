<?php
require_once 'header.php';
require_once 'image_function.php';
$controller = new ControllerRealEstate();
$controllerAgent = new ControllerAgent();
$controllerPropertyType = new ControllerPropertyType();
$controllerPhoto = new ControllerPhoto();
$controllerAuction = new ControllerAuction();

$agents = $controllerAgent->getAgents();
$propertytypes = $controllerPropertyType->getPropertyTypes();

$extras = new Extras();
if (isset($_POST) && !empty($_POST)) {
    $itm = new RealEstate();
    $desired_dir = Constants::IMAGE_UPLOAD_DIR;

    $itm->address = htmlspecialchars(trim(strip_tags($_POST['address'])), ENT_QUOTES);
    $itm->baths = htmlspecialchars(trim(strip_tags($_POST['baths'])), ENT_QUOTES);
    $itm->beds = trim(strip_tags($_POST['beds']));
    $itm->built_in = trim(strip_tags($_POST['built_in']));
    $itm->country = htmlspecialchars(trim(strip_tags($_POST['country'])), ENT_QUOTES);
    $itm->created_at = time();
    $itm->desc1 = '';
    $itm->featured = htmlspecialchars(trim(strip_tags($_POST['featured'])), ENT_QUOTES);
    $itm->lat = trim(strip_tags($_POST['lat']));
    $itm->lon = trim(strip_tags($_POST['lon']));
    $itm->lot_size = trim(strip_tags($_POST['lot_size']));
	if(!empty($_POST['price'])) {
		$itm->price = htmlspecialchars(trim(strip_tags($_POST['price'])), ENT_QUOTES);
	}
    $itm->price_per_sqft = htmlspecialchars(trim(strip_tags($_POST['price_per_sqft'])), ENT_QUOTES);
    $itm->property_type = trim(strip_tags($_POST['property_type']));
    $itm->rooms = trim(strip_tags($_POST['rooms']));
    $itm->sqft = trim(strip_tags($_POST['sqft']));
    $itm->status = 3;
    $itm->updated_at = time();
    $itm->is_deleted = 0;
    $itm->agent_id = trim(strip_tags($_POST['agent_id']));
    //$itm->zipcode = trim(strip_tags($_POST['zipcode']));
    $itm->currency = trim(strip_tags($_POST['currency']));
    $itm->pname = trim(strip_tags($_POST['pname']));
    $itm->pdes = trim(strip_tags($_POST['pdes']));
    $itm->location = '';

    $itemResult = $controller->insertRealEstate($itm);
    if ($itemResult) {
        $realestate_id = $controller->getLastInsertedId();
        if (!empty($realestate_id)) {
            
            $itmAuction = new Auction();
            $itmAuction->property_id = $realestate_id;
			if(!empty($_POST['price'])) {
				$itmAuction->estimate_price = trim(strip_tags($_POST['price']));
            }
			$itmAuction->starting_bid = trim(strip_tags($_POST['starting_bid']));
            $itmAuction->start_time = trim(strip_tags($_POST['start_time']));
            $itmAuction->end_time = trim(strip_tags($_POST['end_time']));
            $itmAuction->created_at = time();
            $controllerAuction->insertAuction($itmAuction);
            
            if (isset($_FILES['img_file']) && !empty($_FILES['img_file'])) {

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

    <body>

        <!-- Fixed navbar -->
        <?php require_once 'menu.php'; ?>

        <div class="container">

            <!-- Example row of columns -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Add Auction</h3>
                </div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <form action="javascript:void(0);" method="POST" class="form_add">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Name of Property</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Property Name" name="pname" required>                             
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Description</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" placeholder="Property Description" name="pdes" required></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Address</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Address" name="address" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Country</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Country" required name="country">
                                    </div>
                                </div>
                                <?php /*
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Postal Code</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Postal Code" required name="zipcode">
                                    </div>
                                </div>
                                 */ ?>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Property Type</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" style="width:100%;" name="property_type">
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
                                    <label class="col-sm-2 col-form-label">Property Value</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Property Value" required name="price_per_sqft">
                                    </div>
                                </div>
								<?php /*
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Estimate</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Estimate" name="price" required>
                                    </div>
                                </div>
								*/ ?>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Bedroom</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Beds" required name="beds" id="website">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Bathroom</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Baths" name="baths" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Rooms</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Rooms" required name="rooms">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Property Size (Sq. Ft)</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Property Size (Sq. Ft)" required name="sqft">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Lot Size (Sq. Ft.)</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Lot Size (Sq. Ft.)" required name="lot_size">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Built In</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Built In" required name="built_in">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Currency</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Currency" name="currency" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="starting_bid" class="col-sm-2 col-form-label">Starting Bid</label>
                                    <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="Starting Bid" name="starting_bid" id="starting_bid"
                                           required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="start_time" class="col-sm-2 col-form-label">Start Date (yyyy-mm-dd hh:ii:ss)</label>
                                    <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="Start Date (yyyy-mm-dd hh:ii:ss) " name="start_time" id="start_time" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="end_time" class="col-sm-2 col-form-label">End Date (yyyy-mm-dd hh:ii:ss)</label>
                                    <div class="col-sm-10">
                                    <input type="text" class="form-control" placeholder="End Date (yyyy-mm-dd hh:ii:ss)" name="end_time" id="end_time" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mortgage_dep_number" class="col-sm-2 col-form-label">Photo Gallery</label>
                                    <input type="hidden" name="property_img" id="property_img">
                                    <div class="col-sm-10">
                                        <div class="main-image-preview_product">
                                            <div class="photo_preview_div">

                                            </div>
                                            <div class="col-sm-12 row">
                                                <div class="file-loading">
                                                    <input id="input-fa" name="property_image[]" type="file" multiple class="deal_image_cropper"  rel ='product'>
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
                                    <label class="col-sm-2 col-form-label">Real Estate Agent</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" style="width:100%;" name="agent_id">
                                            <option value="None">Select Agent</option>
                                            <?php
                                            if ($agents != null) {
                                                foreach ($agents as $agent) {
                                                    echo "<option value='$agent->agent_id'>$agent->name</option>";
                                                }
                                            }
                                            ?>

                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Feature</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" style="width:100%;" name="featured">
                                            <option value="-1">Select if Real Estate will be featured</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>

                                                                
                                <p>
                                    <button type="submit" name="submit" class="btn btn-info" onclick="checkInput()" role="button">Save</button> 
                                    <a class="btn btn-info" href="auction.php" role="button">Cancel</a>
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

            <script>
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
                                            var fieldcount = 0;
                                            var formData = new FormData($('.form_add')[0]);
                                            $.each(imgFiles, function (i, file) {
                                                formData.append('img_file[]', file);
                                            });

                                            $('form.form_add').find('input').each(function () {
                                                if ($(this).prop('required') && $(this).val().trim() == '') {
                                                    fieldcount = fieldcount + 1;
                                                    return false;
                                                }
                                            });
                                            if(fieldcount == 0){
                                                $.ajax({
                                                    type: 'POST',
                                                    url: 'auction_insert.php',
                                                    cache: false,
                                                    contentType: false,
                                                    processData: false,
                                                    data: formData,
                                                    //dataType : 'text',
                                                    dataType: 'json',
                                                    success: function (data)
                                                    {
                                                        window.location.href = 'auction.php';
                                                    }
                                                });
                                            }
                                            return false;


                                        }
            </script>
    </body>
</html>