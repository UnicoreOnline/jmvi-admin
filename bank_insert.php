<?php
require_once 'header.php';
$controller = new ControllerBank();
$controllerMedia = new ControllerMedia();

$extras = new Extras();
if (isset($_POST) && !empty($_POST)) {

    $itm = new Bank();
    $itm->bank_name = trim(strip_tags($_POST['bank_name']));
    $itm->address = trim(strip_tags($_POST['address']));
    $itm->branch_location = trim(strip_tags($_POST['branch_location']));
    $itm->operation_hours = trim(strip_tags($_POST['operation_hours']));
    $itm->contact_number = trim(strip_tags($_POST['contact_number']));
    $itm->mortgage_dep_number = trim(strip_tags($_POST['mortgage_dep_number']));
    $itm->created_at = date('Y-m-d H:i:s');

    $controller->insertBank($itm);

    $bank = $controller->getLastInsertedId();
    $desired_dir = Constants::IMAGE_UPLOAD_DIR;
    if (!empty($bank) && !empty($bank['id'])) {
        
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

                $new_file_name = "bank_".$key . $timestamp . "." . $extension;
                
                if (is_dir($desired_dir) == false) {
                    // Create directory if it does not exist
                    mkdir("$desired_dir", 0700);
                }

                move_uploaded_file($file_tmp, $desired_dir . "/" . $new_file_name);

                $media = new Media();
                $media->file_name = $new_file_name;                
                $media->ref_table = 1;
                $media->ref_id = $bank['id'];
                $media->created_at = date("Y-m-d H:i:s");
                $controllerMedia->insertMedia($media);
                
                if($key == 0){
                    $itm->id = $bank['id'];
                    $itm->logo = $new_file_name;
                    $controller->updateBankLogo($itm);
                }
                
            }
        }
        
    }
    
    $response = [
        'status' => '200',
        'message' => 'Bank Added Successfully'
    ];
    echo json_encode($response);exit;    
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" href="http://getbootstrap.com/assets/ico/favicon.ico">

        <title>RealEstate Finder</title>

        <!-- Bootstrap core CSS -->
        <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
        <!-- Custom styles for this template -->
        <link href="bootstrap/css/navbar-fixed-top.css" rel="stylesheet">
        <link href="bootstrap/css/custom.css" rel="stylesheet">
        <link href="bootstrap/css/fileinput.css" rel="stylesheet">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" crossorigin="anonymous">
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_API_KEY; ?>&sensor=false"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>


        <!-- Just for debugging purposes. Don't actually copy this line! -->
        <!--[if lt IE 9]>
        <script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

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
                    <h3 class="panel-title">Add Bank</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="javascript:void(0);" method="POST" class="bank_add form_add" id="bank_add">
                                <div class="form-group row">
                                    <label for="bank_name" class="col-sm-2 col-form-label">Bank Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-plaintext" placeholder="Bank Name" name="bank_name" id="bank_name"
                                               required >                              
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address" class="col-sm-2 col-form-label">Address</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" placeholder="Address" name="address" id="address" required></textarea>                                                                  
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="branch_location" class="col-sm-2 col-form-label">Branch Location</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-plaintext" placeholder="Branch Location" name="branch_location" id="branch_location"
                                               required >                              
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="operation_hours" class="col-sm-2 col-form-label">Hours of Operation</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-plaintext" placeholder="Hours of Operation" name="operation_hours" id="operation_hours"
                                               required >                              
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="contact_number" class="col-sm-2 col-form-label">Contact Number</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-plaintext" placeholder="Contact Number" name="contact_number" id="contact_number"
                                               required >                              
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mortgage_dep_number" class="col-sm-2 col-form-label">Mortgage Dept Number</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-plaintext" placeholder="Mortgage Dept Number" name="mortgage_dep_number" id="mortgage_dep_number"
                                               required >                              
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="mortgage_dep_number" class="col-sm-2 col-form-label">Photo Gallery</label>
                                    <input type="hidden" name="bank_img" id="bank_img">
                                    <div class="col-sm-10">
                                        <div class="main-image-preview_product">
                                            <div class="photo_preview_div">

                                            </div>
                                            <div class="col-sm-12 row">
                                                <div class="file-loading">
                                                    <input id="input-fa" name="bank_image[]" type="file" multiple class="deal_image_cropper"  rel ='product'>
                                                </div>                             
                                            </div>
                                        </div>                                        
                                    </div>
                                </div>                                
                                <p>
                                    <button type="submit" name="submit" class="btn btn-info" onclick="checkInput();"
                                            role="button">Save
                                    </button>
                                    <a class="btn btn-info" href="auction.php" role="button">Cancel</a>
                                </p>
                            </form>


                        </div>
                    </div>
                </div>


            </div> <!-- /container -->



            
        </div>
        <?php require_once 'footer_js.php'; ?>
        <!-- Bootstrap core JavaScript
            ================================================== -->
            <!-- Placed at the end of the document so the pages load faster -->            
            <script src="bootstrap/js/fileinput.js"></script>
            <script src="bootstrap/js/theme.js"></script>
            <script>
                                        var imgFiles = [];

                                        $("#input-fa").fileinput({
                                            uploadUrl: "/bank_insert.php",
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
                                                    url: 'bank_insert.php',
                                                    cache: false,
                                                    contentType: false,
                                                    processData: false,
                                                    data: formData,
                                                    //dataType : 'text',
                                                    dataType: 'json',
                                                    success: function (data)
                                                    {
                                                        window.location.href = 'bank.php';
                                                    }
                                                });
                                            }
                                            return false;


                                        }
            </script>
            
            
    </body>
</html>