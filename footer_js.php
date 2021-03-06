<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="bootstrap/js/jquery.js"></script>
<script src="bootstrap/js/bootstrap.js"></script>
<div class="cover" style="display:none;"></div>
<div class="loader" style="display:none;"><img src="images/loader.gif"></div>
<script>
    $(document).bind("ajaxSend", function(){
        $(".cover").show();
        $(".loader").show();
    }).bind("ajaxComplete", function(){
        $(".cover").hide();
        $(".loader").hide();
    });
</script>