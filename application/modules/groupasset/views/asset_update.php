<div class="modal-dialog">
        
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title"> Groupasset - Edit </h4>
</div>
<div class="modal-body">

 <!-- error div -->
 <div class="alert alert-success success"> </div>
 <div class="alert alert-warning warning"> </div>
 <div class="alert alert-error error"> </div>
 
 <!-- form add -->
<div class="x_panel" >
<div class="x_title">
  
  <div class="clearfix"></div> 
</div>
<div class="x_content">
    
<?php
    
$atts1 = array(
	  'class'      => 'btn btn-primary button_inline',
	  'title'      => 'COA - List',
	  'width'      => '600',
	  'height'     => '400',
	  'scrollbars' => 'yes',
	  'status'     => 'yes',
	  'resizable'  => 'yes',
	  'screenx'    =>  '\'+((parseInt(screen.width) - 600)/2)+\'',
	  'screeny'    =>  '\'+((parseInt(screen.height) - 400)/2)+\'',
);

?>

 <form id="upload_form_edit" data-parsley-validate class="form-horizontal form-label-left" method="POST" 
 action="<?php echo $form_action_update; ?>" enctype="multipart/form-data">

      <!-- pembatas div -->
      <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
          <input type="hidden" id="tid" name="tid">
      </div>
       <!-- pembatas div -->

      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Code </label>
        <div class="col-md-4 col-sm-4 col-xs-12">
          <input type="text" name="tcode" id="tcode" class="form-control" placeholder="Code">
        </div>
      </div>

      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Name </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <input type="text" name="tname" id="tname" class="form-control" placeholder="Name">
        </div>
      </div>

     <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Period </label>
        <div class="col-md-2 col-sm-4 col-xs-12">
          <input type="number" name="tperiod" id="tperiod" class="form-control" placeholder="">
        </div>
      </div>

      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Description </label>
        <div class="col-md-9 col-sm-9 col-xs-12">
<textarea name="tdesc" id="tdesc" class="form-control" rows="3" placeholder="Description"><?php echo set_value('tdesc', isset($default['desc']) ? $default['desc'] : ''); ?></textarea>
        </div>
      </div>

      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Accumulation </label>
        <div class="col-md-9 col-sm-9 col-xs-12">
<input type="text" name="taccumulation" id="titem_update" class="form-control" required readonly style="max-width:120px; float:left;"> 
<?php echo anchor_popup(site_url("account/get_list/titem_update/"), '[ ... ]', $atts1); ?> 
        </div>
      </div>

      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Depreciation </label>
        <div class="col-md-9 col-sm-9 col-xs-12">
<input type="text" name="tdepreciation" id="titem6_update" class="form-control" required readonly style="max-width:120px; float:left;"> 
<?php echo anchor_popup(site_url("account/get_list/titem6_update/"), '[ ... ]', $atts1); ?> 
        </div>
      </div>

      <div class="ln_solid"></div>
      <div class="form-group">
        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3 btn-group">
          <button type="submit" class="btn btn-primary" id="button">Save</button>
          <button type="button" id="bclose" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="button" id="breset" class="btn btn-warning" onClick="reset();">Reset</button>
        </div>
      </div>
         
</form> 

</div>
</div>
<!-- form add -->

</div>
    <div class="modal-footer"> </div>
</div>
  
</div>