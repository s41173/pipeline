<div class="modal-dialog">
        
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title"> Add New Branch </h4>
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
    
<form id="upload_form_non" data-parsley-validate class="form-horizontal form-label-left" method="POST" action="<?php echo $form_action; ?>" 
      enctype="multipart/form-data">
   
          <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
            <input type="text" class="form-control has-feedback-left" id="tname" name="tname" placeholder="Name">
            <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span> 
          </div>

          <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
            <input type="email" class="form-control" id="tmail" name="tmail" placeholder="Email">
            <span class="fa fa-envelope form-control-feedback right" aria-hidden="true"></span> 
          </div>
    
                  <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
                    <input type="tel" name="tphone" class="form-control has-feedback-left" id="tphone" placeholder="Phone">
                    <span class="fa fa-phone form-control-feedback left" aria-hidden="true"></span> 
                  </div>
                  
                  <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
                    <input type="tel" name="tmobile" class="form-control" id="tmobile" placeholder="Mobile">
                    <span class="fa fa-mobile form-control-feedback right" aria-hidden="true"></span> 
                  </div>
                    
                  <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
                    <?php $js = "class='select2_single form-control' id='ccity' tabindex='-1' style='width:100%;' "; 
			        echo form_dropdown('ccity', $city, isset($default['city']) ? $default['city'] : '', $js); ?>
                  </div>
    
                  <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
                    <input type="text" name="tzip" class="form-control" id="tzip" placeholder="Zip Code">
                    <span class="fa fa-file-archive-o form-control-feedback right" aria-hidden="true"></span> 
                  </div>
                  
                  <!-- pembatas div -->
                  <div class="col-md-6 col-sm-6 col-xs-12 form-group has-feedback">
                    
                  </div>
                   <!-- pembatas div -->
                  
                  <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12"> Branch Code </label>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                      <input type="text" name="tcode" id="tcode" class="form-control" placeholder="Branch Code">
                    </div>
                  </div>
    
                  <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12"> Address </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
<textarea name="taddress" id="taddress" class="form-control" rows="3" placeholder="Address"><?php echo set_value('taddress', isset($default['address']) ? $default['address'] : ''); ?></textarea>
                    </div>
                  </div>
    
                  <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12"> Sales - Acc </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
<input type="text" name="tsalesacc" id="titem" class="form-control" required readonly style="max-width:120px; float:left;"> 
<?php echo anchor_popup(site_url("account/get_list/titem/"), '[ ... ]', $atts1); ?> 
                    </div>
                  </div>
     
                  <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12"> Stock - Acc </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
<input type="text" name="tstockacc" id="titem2" class="form-control" required readonly style="max-width:120px; float:left;"> 
<?php echo anchor_popup(site_url("account/get_list/titem2/"), '[ ... ]', $atts1); ?> 
                    </div>
                  </div>
    
                  <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12"> Unit Cost - Acc </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
<input type="text" name="tunitacc" id="titem3" class="form-control" required readonly style="max-width:120px; float:left;"> 
<?php echo anchor_popup(site_url("account/get_list/titem3/"), '[ ... ]', $atts1); ?> 
                    </div>
                  </div>
    
                  <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12"> AR - Acc </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
<input type="text" name="taracc" id="titem4" class="form-control" required readonly style="max-width:120px; float:left;"> 
<?php echo anchor_popup(site_url("account/get_list/titem4/"), '[ ... ]', $atts1); ?> 
                    </div>
                  </div>
    
                  <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12"> Bank - Acc </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
<input type="text" name="tbankacc" id="titem5" class="form-control" required readonly style="max-width:120px; float:left;"> 
<?php echo anchor_popup(site_url("account/get_list/titem5/"), '[ ... ]', $atts1); ?> 
                    </div>
                  </div>
    
                  <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12"> Cash - Acc </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
<input type="text" name="tcashacc" id="titem6" class="form-control" required readonly style="max-width:120px; float:left;"> 
<?php echo anchor_popup(site_url("account/get_list/titem6/"), '[ ... ]', $atts1); ?> 
                    </div>
                  </div>
    
                  <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12"> Image </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
	      <input type="file" id="uploadImage" accept="image/*" class="input-medium" title="Upload" name="userfile" /> <br>
          <img id="catimg" style=" max-width:50px; height:auto;">
                    </div>
                  </div>
                  
                  <div class="ln_solid"></div>
                  <div class="form-group">
                    <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
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