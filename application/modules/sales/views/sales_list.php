<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title> Sales - List </title>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="<?php echo base_url(); ?>js/bs/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="<?php echo base_url(); ?>js/bs/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>


<script type="text/javascript" src="<?php echo base_url();?>js-old/register.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js-old/jquery.min.js"></script>
<!--<script type="text/javascript" src="<?php //echo base_url();?>js-old/complete.js"></script> -->
<script type="text/javascript" src="<?php echo base_url();?>js-old/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>js-old/sortir.js"></script> 

<script type="text/javascript">
var uri = "<?php echo site_url('ajax')."/"; ?>";
var baseuri = "<?php echo base_url(); ?>";
    
$(document).ready(function() 
    { 
        $("#myTable").tablesorter(); 
    } 
); 
    
function closeWindow() {
setTimeout(function() {
window.close();
}, 30000);
}
    
</script>    
    
</head>

<body onload="closeWindow()">
    
<div class="container-fluid">
    
    <style type="text/css">
        .border{ border: 0px solid red; }
        .acctable{ width: 100%; margin: 25px 0 20px 0;  } 
        .field{ margin: 10px;}
    </style>
    
    <div class="row">
        <div class="col-lg-12 border">
        
    <fieldset class="field"> <legend> Sales List </legend>
    <form name="modul_form" class="form-inline" method="post" action="">              
              
        
    </form>
    </fieldset>

    <?php echo ! empty($table) ? $table : ''; ?>
    <button type="button" onclick="window.close()" class="btn btn-danger">  Close </button> <br> <br>
        </div>
    </div>
    
</div>



    
    
</body>

</html>