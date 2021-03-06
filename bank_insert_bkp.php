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
        if (isset($_POST['bank_img']) && !empty($_POST['bank_img'])) {
            $images = json_decode($_POST['bank_img'], true);
            foreach ($images as $ikey => $img) {
                $key = $img['newFileIndex'];
                $media = new Media();

                $file_name = $_FILES['gfile']['name'][$key];
                $file_size = $_FILES['gfile']['size'][$key];
                $file_tmp = $_FILES['gfile']['tmp_name'][$key];
                $file_type = $_FILES['gfile']['type'][$key];

                $timestamp = time();
                $temp = explode(".", $file_name);
                $extension = end($temp);

                $new_file_name = "bank_" . $timestamp . "." . $extension;
                if (is_dir($desired_dir) == false) {
                    // Create directory if it does not exist
                    mkdir("$desired_dir", 0700);
                }
                
                move_uploaded_file($file_tmp, $desired_dir . "/" .$new_file_name);
                $media->file_name = $new_file_name;                
                $media->ref_table = 1;
                $media->ref_id = $bank['id'];
                $media->created_at = date("Y-m-d H:i:s");
                $controllerMedia->insertMedia($media);
                
                if($ikey == 0){
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
                            <form action="javascript:void(0);" method="POST" class="bank_add" id="bank_add">
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
                                            <div class="col-sm-2 row">
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
                                        $("#input-fa").fileinput({
                                            theme: "fa",
                                            enableResumableUpload: true,
                                            allowedFileExtensions: ['jpg', 'png', 'jpeg'],
                                            showPreview: true,
                                            showRemove: true,
                                            showUpload: false,
                                            showCaption: false,
                                            browseOnZoneClick: true,
                                            showBrowse: false,
                                            overwriteInitial: false
                                        });
                                        var photoArray = [];
                                        var galimageJson = [];
                                        var galFiles = [];
                                        modifyDropZone1();
                                        var newFileIndex = 0;
                                        var srchKey = '<?php (isset($srchKey) ? ($srchKey + 1) : 0) ?>';
                                        var productItem = srchKey;
                                        var catItem = 0;
                                        var imgItem = '';
                                        var req_width = 300;
                                        var req_height = 400;
                                        var req_ratio = (req_width / req_height);
                                        req_ratio = req_ratio.toFixed(3);
                                        var regex = /^([a-zA-Z0-9\s_\\.\-_-_)( @])+(.jpg|.jpeg|.png)$/;

                                        function modifyDropZone1() {
                                            $('.fileinput-remove').hide();

                                            var htmlProd = '<i class=\"fa fa-plus\" aria-hidden=\"true\"></i>';

                                            var dropZoneHtml = '<div class=\"upload_content\">';
                                            dropZoneHtml += '<i class=\"fa fa-plus\" aria-hidden=\"true\"></i>';
                                            dropZoneHtml += '</div>';

                                            $('.file-drop-zone').html(dropZoneHtml);

                                            $('.main-image-preview_product').find('.upload_content').html(htmlProd);
                                        }

                                        $(document).on('fileloaded', '.deal_image_cropper', function (event, file, previewId, index, reader) {

                                            var galImge = {'isNewImage': 1, 'media_id': '', 'newFileIndex': newFileIndex, 'index': productItem};
                                            galimageJson.push(galImge);
                                            $('#bank_img').val(JSON.stringify(galimageJson));
                                            galFiles.push(file);

                                            loadOriginalImageView(newFileIndex, reader.result, 'adjust_crop', previewId, $.trim($(this).attr('rel')), 0);

                                            $('#' + previewId).parent().parent().parent().parent().addClass('hide');
                                            modifyDropZone1();
                                        });

                                        $(document).on('fileloaded', '.deal_image_cropper_multiple', function (event, file, previewId, index, reader) {

                                            var fileName = file.name;
                                            if (!regex.test(fileName.toLowerCase())) {
                                                //$('.new_uploaded_img').remove();
                                                $('#file-upload .file-input.has-error').removeClass('has-error');
                                                //alert('Please upload photo with (.jpg/.jpeg/.png) extension');            
                                                return false;
                                            }
                                            galFiles.push(file);
                                            var galImge = {'isNewImage': 1, 'media_id': '', 'newFileIndex': newFileIndex, 'index': productItem};
                                            galimageJson.push(galImge);
                                            $('#bank_img').val(JSON.stringify(galimageJson));

                                            loadOriginalImageView(newFileIndex, reader.result, 'adjust_crop', previewId, $.trim($(this).attr('rel')), 1, productItem);
                                            $('#' + previewId).addClass('hide');
                                            console.log(galFiles);
                                            modifyDropZone1();
                                        });

                                        $(document).on('filebatchselected', '.deal_image_cropper_multiple', function (event, files) {

                                            $.each(files, function (index, value) {
                                                var fileName1 = value.name;
                                                if (!regex.test(fileName1.toLowerCase())) {
                                                    //$('.new_uploaded_img').remove();
                                                    $('#file-upload .file-input.has-error').removeClass('has-error');
                                                    $('.deal_image_cropper_multiple').fileinput('reset');
                                                    modifyDropZone1();
                                                    alert('Invalid file type, please upload photo(s) with .jpg/.jpeg/.png extensions');
                                                    return false;
                                                }
                                            });
                                        });


                                        function loadOriginalImageView(preview_index, src, cropClass, itemId, rel = '', isMultiple = 0, itemIndex = 0){
                                            var html = '';
                                            var actionClass = 'delete-image';
                                            var nameClass = 'DynamicModel[' + rel + ']';
                                            var imgClass = rel + '_img';

                                            if (isMultiple) {
                                                actionClass = 'delete-image-multiple';
                                                nameClass = 'DynamicModel[' + rel + '][' + itemIndex + ']';
                                                imgClass = imgClass + '_' + itemIndex;

                                                html += '<div class=\"image_preview_div col-sm-2 mr_top_10 new_uploaded_img item_index_' + itemIndex + '\" id=\"' + itemIndex + '_image_preview_div\">';
                                                html += '<div class=\"col-sm-4 margin-bottom-25 item_index_' + itemIndex + '\">';
                                                html += '<div class=\"inner-layer mr_top_10\">';
                                            }
                                            html += '<div class=\"image_preview_div2 col-sm-2\" id=\"' + preview_index + '_image_preview_div\">';
                                            html += '<div class=\"img-box\"> <img width=\"100%\" src=\"' + src + '\" id=\"banner_image_' + preview_index + '\"/ class=\"added_deal_image ' + imgClass + '\"> </div>';
                                            html += '<div class=\"img-action\">';
                                            html += '<span class=\"delete-image btn btn-danger mr_top_10 ' + actionClass + '\" item-index=' + itemIndex + '><i class=\"fa fa-minus\" aria-hidden=\"true\" id=\"delete-' + preview_index + '-image\" data-input-index=\"' + preview_index + '\" rel-data-id=' + itemId + ' item-rel=' + rel + ' item-index=' + itemIndex + '></i></span>';
                                            html += '</div>';

                                            html += '</div>';
                                            html += '</div>';
                                            if (isMultiple) {
                                                html += '</div>';
                                                html += '</div>';
                                            } else {
                                                //$('.main-image-preview_' + rel).parent().find('.file-input').addClass('hide');
                                            }

                                            if (cropClass == 'adjust_crop') {
                                                newFileIndex = newFileIndex + 1;
                                            }
                                            $('.main-image-preview_' + rel + ' .photo_preview_div').append(html);

                                            productItem++;
                                            modifyDropZone1();
                                        }



                                        $(document).on('click', '.delete-image', function () {
                                            var delIdx = $(this).attr('data-input-index');
                                            var itemRel = $(this).attr('item-rel');
                                            $(this).closest('.image_preview_div2').remove();

                                            modifyDropZone1();

                                            return false;
                                        });





            </script>
            <script>
                function checkInput() {
                    if ($('.bank_add').find('.has-error').length) {
                        return false;
                    }
                    var formData = new FormData($('#bank_add')[0]);
                    $.each(galFiles, function (i, file) {
                        formData.append('gfile[]', file);
                    });
                    var fieldcount = 0;
                    $('form.bank_add').find('input').each(function () {
                        if ($(this).prop('required') && $(this).val().trim() == '') {
                            fieldcount = fieldcount + 1;
                            return false;
                        } else {
                            console.log('neee');
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