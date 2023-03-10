<div class="modal-dialog">
        
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title"> Purchase - Report </h4>
</div>
<div class="modal-body">
 
 <!-- form add -->
<div class="x_panel" >
<div class="x_title">
  
  <div class="clearfix"></div>
</div>
<div class="x_content">

<form id="" data-parsley-validate class="form-horizontal form-label-left" method="POST" 
action="<?php echo $form_action_report; ?>" enctype="multipart/form-data">
    
     <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Period </label>
        <div class="col-md-9 col-sm-9 col-xs-12">     
<input type="text" readonly style="width: 200px" name="reservation" id="d1" class="form-control active" value=""> 
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Vendor </label>
        <div class="col-md-8 col-sm-9 col-xs-12">     
<?php $js = "class='select2_single form-control' id='cvendor' tabindex='-1' style='width:100%;' "; 
echo form_dropdown('cvendor', $vendor, isset($default['vendor']) ? $default['vendor'] : '', $js); ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Currency </label>
        <div class="col-md-3 col-sm-12 col-xs-12">     
            <?php $js = "class='form-control' id='ccur' tabindex='-1' style='min-width:170px;' "; 
            echo form_dropdown('ccurrency', $currency, isset($default['currency']) ? $default['currency'] : '', $js); ?>
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Account </label>
        <div class="col-md-4 col-sm-9 col-xs-12">     
    <select name="cacc" class="form-control">
        <option value=""> -- </option>
        <option value="bank"> Bank </option>
        <option value="cash"> Cash </option>
        <option value="pettycash"> Petty Cash </option>
    </select>
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Status </label>
        <div class="col-md-4 col-sm-9 col-xs-12">     
    <select name="cstatus" class="form-control">
        <option value=""> -- </option>
        <option value="0"> Debt </option>
        <option value="1"> Settled </option>
        <option value="pettycash"> Petty Cash </option>
    </select>
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Report Type </label>
        <div class="col-md-4 col-sm-9 col-xs-12">     
    <select name="ctype" class="form-control">
       <option value="0" selected="selected"> Summary </option>
       <option value="1"> Details </option>
       <option value="2"> Pivotable </option>
    </select>
        </div>
    </div>

      <div class="ln_solid"></div>
      <div class="form-group">
          <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
          <div class="btn-group"> 
           <button type="submit" class="btn btn-primary">Post</button>
           <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          </div>
          </div>
      </div>
    
  </form> 
  <div id="err"></div>
</div>
</div>
<!-- form add -->

</div>
    <div class="modal-footer"> </div>
</div>
  
</div>