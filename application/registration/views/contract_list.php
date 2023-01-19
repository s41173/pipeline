<html>
<head>    
<!-- bootstrap basic -->

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="<?php echo base_url(); ?>js/bs/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="<?php echo base_url(); ?>js/bs/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script type="text/javascript" src="<?php echo base_url();?>js-old/register.js"></script>
    
<style type="text/css">@import url("<?php echo base_url() . 'css/select/select2.min.css'; ?>");</style>
    
<title> Product List </title>    
</head>
<body onload="closeWindow()">
  <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12 border">

  <fieldset class="field"> <legend> Contract Filter </legend>
    <form name="modul_form" class="form-inline" method="post" action="<?php echo $form_action ?>">                        
             <div class="form-group">
<label> Filter Type </label> <br>
<select name="ctype" class="form-control">
    <option value=""> -- </option>
    <option value="0"> DO </option>
    <option value="1"> Origin No </option>
    <option value="2"> Picking Name </option>
    <option value="3"> Doc No </option>
    <option value="4"> Contract No </option>
</select>
              </div>
        
             <div class="form-group">
<label> Value </label> <br>
<input type="text" class="form-control" name="tvalue" style="width:300px;">
              </div>
        
              
          <div class="btn-group"> <label>.</label> <br>
           <button type="submit" class="btn btn-primary button_inline"> Filter </button>
           <a href="<?php echo site_url('registration/get_list/titem'); ?>" class="btn btn-success button_inline"> Reset </a>
           <button onclick="window.close()" type="button" class="btn btn-danger button_inline"> Close </button>
          </div>
        
    </form>
  </fieldset>            
     
    <style type="text/css">
        #example{ font-size: 13px;}
    </style>        
    <?php echo ! empty($table) ? $table : ''; ?>        
                
  <script type="text/javascript" charset="utf8" src="<?php echo base_url(); ?>js/bs/datatable/jquery-1.12.4.js"></script>
  <script type="text/javascript" charset="utf8" src="<?php echo base_url(); ?>js/bs/datatable/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url(); ?>js/bs/datatable/dataTables.bootstrap.min.js"></script>
    
  <script type="text/javascript">

  function closeWindow() {
setTimeout(function() {
window.close();
}, 120000);
}      
      
  $(function(){
    $("#example").dataTable();
  })
      
    $(document).ready(function() {
		
      $(".select2_single").select2({
        placeholder: "-- Select Option --",
        allowClear: true
      });
	  
      $(".select22_single").select2({
        placeholder: "-- Option --",
        allowClear: true
      });
	  
      $(".select2_multiple").select2({
        maximumSelectionLength: 10,
        placeholder: "With Max Selection limit 10",
        allowClear: true
      });
    });
      
  </script>

</div>     
      </div>
    </div>
    
<script src="<?php echo base_url();?>js/select/select2.full.js"></script>
    
</body>
</html>