<div class="modal-dialog">
        
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title"> New Transaction </h4>
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
     
    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Code </label>
      <div class="col-md-3 col-sm-6 col-xs-12">
<input id="tno" class="form-control col-md-12 col-xs-12" type="text" readonly name="tcode" value="<?php echo $code; ?>">
      </div>
    </div>
    
     <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Type </label>
      <div class="col-md-5 col-sm-6 col-xs-12">
          <select name="ctype" class="form-control">
              <option value="PIPELINE"> PIPELINE </option>
              <option value="CARRIAGE"> CARRIAGE </option>
          </select>
      </div>
    </div>
    
    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Docno </label>
      <div class="col-md-6 col-sm-6 col-xs-12">
<input id="tno" class="form-control col-md-12 col-xs-12" type="text" name="tdocno">
      </div>
    </div>
    
    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Date </label>
      <div class="col-md-4 col-sm-6 col-xs-12">
        <input id="ds4" class="form-control col-md-4 col-xs-12" type="text" name="tdate" required placeholder="Date">
      </div>
    </div>

    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Source-Tank </label>
      
        <table>
            <tr>  
                <td> 
                  <div class="col-md-5 col-sm-6 col-xs-12">  
<?php $js = "class='form-control select2_single' id='ccust' tabindex='-1' style='width:100px; margin:0;' "; 
         echo form_dropdown('csrctank', $srctank, isset($default['']) ? $default[''] : '', $js); ?>
                  </div>
                </td>
                <td>
                <div class="col-md-6 col-sm-6 col-xs-12">  
         <input id="tsrctank" class="form-control col-md-12 col-xs-12" type="text" name="tsrctank">  
                </div>
                </td>
            </tr>
        </table>  
    </div>
    
    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Dest-Tank </label>
      <div class="col-md-5 col-sm-5 col-xs-12">
         <?php $js = "class='form-control select2_single' id='ccust' tabindex='-1' style='width:100%;' "; 
         echo form_dropdown('cttotank', $tank, isset($default['']) ? $default[''] : '', $js); ?>
      </div>
    </div>
    
    <div class="form-group">
      <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-12"> Note </label>
      <div class="col-md-6 col-sm-6 col-xs-12">
        <textarea id="tnote" name="tnote" rows="3" class="form-control"></textarea>
      </div>
    </div>
     
      <div class="ln_solid"></div>
      <div class="form-group">
          <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3 btn-group">
          <button type="submit" class="btn btn-primary" id="button">Save</button>
          <button type="button" id="bclose" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="reset" id="breset" class="btn btn-warning">Reset</button>
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