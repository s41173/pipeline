<div class="modal-dialog">
        
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title"> Shore Calculation Report </h4>
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
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Type </label>
        <div class="col-md-7 col-sm-9 col-xs-12">
         <select name="ctranstype" id="ctype_search" class="select2_single form-control" style="width:150px;">
           <option value="0"> Intertank </option>
           <option value="1"> Ship Outward </option>
           <option value="2"> Ship Inward </option>
           <option value="3"> Outward-3-Party </option>
        </select>
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Consignee </label>
        <div class="col-md-7 col-sm-9 col-xs-12">
          <?php $js = "class='form-control select2_single' id='ccust_search' tabindex='-1' style='width:240px;' "; 
          echo form_dropdown('ccust', $customer, isset($default['']) ? $default[''] : '', $js); ?>
        </div>
    </div>
        
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Period </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
          <table>
           <tr> 
               <td>
<select name="creporttype" class="form-control">
   <option value="dates"> Trans Date </option>
   <option value="eta"> ETA </option>
   <option value="etb"> ETB </option>
   <option value="laycan"> Laycan </option>
   <option value="heating"> Heating </option>
   <option value="start_pump"> Comm Pump </option>    
</select>
               </td>
           </tr>
           <tr>
<td>
    <input type="text" readonly style="width: 200px" name="reservation" id="d1" class="form-control active" value=""> 
</td>
           </tr>      
          </table>
        </div>
    </div>
    
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Type </label>
        <div class="col-md-4 col-sm-9 col-xs-12">     
			<select name="ctype" class="form-control">
              <option value="0"> Summary </option>
              <option value="1"> Summary Addition </option>
              <option value="2"> Pivottable </option>
              <option value="3"> Pivottable Addition </option>
            </select>
        </div>
    </div>

      <div class="ln_solid"></div>
      <div class="form-group">
          <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
          <button type="submit" class="btn btn-primary">Post</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          </div>
      </div>
  </form> 
  <div id="err"></div>
</div>
</div>
<!-- form add -->

</div>
    <div class="modal-footer">
      
    </div>
  </div>
  
</div>