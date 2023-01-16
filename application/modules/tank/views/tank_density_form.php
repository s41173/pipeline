<div class="modal-dialog">
        
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title"> Add Density </h4>
</div>
<div class="modal-body">

 <!-- error div -->
 <div class="alert alert-success success"> </div>
 <div class="alert alert-warning warning"> </div>
 <div class="alert alert-error error"> </div>
 
 <!-- form add -->
<div class="x_panel" >
<div class="x_content">

<form id="ajaxformdensity" data-parsley-validate class="form-horizontal form-label-left" method="POST" action="<?php echo site_url('tank/add_density/'.$uniqueid); ?>" 
enctype="multipart/form-data"> 
    
      <div class="col-md-9 col-sm-9 col-xs-12 form-group">
        <label class="control-label col-md-4 col-sm-3 col-xs-12"> Temperature (&#8451;) </label>
        <div class="col-md-5 col-sm-5 col-xs-12">
       <input type="text" class="form-control" name="ttanksuhu">
        </div>
      </div>
          
      <div class="col-md-9 col-sm-9 col-xs-12 form-group">
        <label class="control-label col-md-4 col-sm-3 col-xs-12"> Density (kg/l) </label>
        <div class="col-md-5 col-sm-5 col-xs-12">
      <input type="text" class="form-control" name="ttankdensity" required>
        </div>
      </div>
    
      <div class="clearfix"></div> 
      
      <div class="ln_solid"></div>
      <div class="form-group">
        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3 btn-group">
          <button type="submit" class="btn btn-primary" id="button">Save</button>
          <button type="button" id="bclose" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="reset" id="breset" class="btn btn-success">Reset</button>
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