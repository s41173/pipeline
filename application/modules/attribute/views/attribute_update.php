<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel"> Category - Update </h4>
        </div>
        
 <div class="modal-body"> 
 
 <!-- error div -->
 <div class="alert alert-success success"> </div>
 <div class="alert alert-warning warning"> </div>
 <div class="alert alert-error error"> </div>

 <!-- form edit -->
 <form id="edit_form_non" data-parsley-validate class="form-horizontal form-label-left" method="POST" 
 action="<?php echo $form_action_update; ?>" enctype="multipart/form-data">
         
    <div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12"> Category </label>
    <div class="col-md-9 col-sm-9 col-xs-12">
      <?php $js = "class='select2_single form-control' id='ccategory_update' tabindex='-1' style='width:100%;' "; 
       echo form_dropdown('ccategory', $category, isset($default['category']) ? $default['category'] : '', $js); ?>
    </div>
    </div>
    
    <div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12"> Attribute Group </label>
    <div class="col-md-9 col-sm-9 col-xs-12">
      <?php $js = "class='select2_single form-control' id='cattribute_update' tabindex='-1' style='width:100%;' "; 
       echo form_dropdown('cattribute', $attribute, isset($default['attribute']) ? $default['attribute'] : '', $js); ?>
    </div>
    </div>
    
    <div class="form-group">
    <label class="control-label col-md-3 col-sm-3 col-xs-12"> Order </label>
    <div class="col-md-9 col-sm-9 col-xs-12">
      <input type="number" name="torder" class="form-control" id="torder_update" placeholder="Order">
    </div>
    </div>

      <div class="ln_solid"></div>
      <div class="form-group">
          <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3 btn-group">
          <button type="submit" class="btn btn-primary" id="button">Save</button>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
          </div>
      </div>
  </form> 
  <!-- form edit -->
  
  </div>
 </div>
</div>