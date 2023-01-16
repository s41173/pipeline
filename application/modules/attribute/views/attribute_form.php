<div class="modal-dialog">
        
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title"> Add New Attribute Group </h4>
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

<form id="upload_form_non" data-parsley-validate class="form-horizontal form-label-left" method="POST" action="<?php echo $form_action; ?>" 
      enctype="multipart/form-data">
    
    <div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12"> Category </label>
    <div class="col-md-9 col-sm-9 col-xs-12">
      <?php $js = "class='select2_single form-control' id='ccategory' tabindex='-1' style='width:100%;' "; 
       echo form_dropdown('ccategory', $category, isset($default['category']) ? $default['category'] : '', $js); ?>
    </div>
    </div>
    
    <div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12"> Attribute Group </label>
    <div class="col-md-9 col-sm-9 col-xs-12">
      <?php $js = "class='select2_single form-control' id='cattribute' tabindex='-1' style='width:100%;' "; 
       echo form_dropdown('cattribute', $attribute, isset($default['attribute']) ? $default['attribute'] : '', $js); ?>
    </div>
    </div>
    
    <div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12"> Order </label>
    <div class="col-md-9 col-sm-9 col-xs-12">
      <input type="number" name="torder" class="form-control" id="torder" placeholder="Order">
    </div>
    </div>


      <div class="ln_solid"></div>
      <div class="form-group">
          <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3 btn-group">
          <button type="submit" class="btn btn-primary" id="button">Save</button>
          <button type="button" id="bclose" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="button" id="breset" class="btn btn-warning">Reset</button>
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