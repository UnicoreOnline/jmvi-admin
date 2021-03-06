<?php
    require_once 'header.php';
    $controller = new ControllerBank();
    if(isset($_POST) && isset($_POST['key']) && !empty($_POST['key'])){
        $photo_id = $_POST['key'];        
        $controller->deleteMdeia($photo_id,1);
    }
    
   echo json_encode(['message'=>'Deleted file successfully!']);
?>