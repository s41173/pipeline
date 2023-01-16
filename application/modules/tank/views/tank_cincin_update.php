<div class="modal-dialog">
        
<!-- Modal content-->
<div class="modal-content">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h4 class="modal-title"> Edit Cincin </h4>
</div>
<div class="modal-body">

 <!-- error div -->
 <div class="alert alert-success success"> </div>
 <div class="alert alert-warning warning"> </div>
 <div class="alert alert-error error"> </div>
 
 <!-- form add -->
<div class="x_panel" >
<div class="x_content">

<form id="ajaxformcincin1" data-parsley-validate class="form-horizontal form-label-left" method="POST" action="<?php echo site_url('tank/cincin_update_process/'.$uniqueid); ?>" 
enctype="multipart/form-data"> 
    
      <div class="col-md-9 col-sm-9 col-xs-12 form-group">
        <label class="control-label col-md-4 col-sm-3 col-xs-12"> Ring No </label>
        <div class="col-md-5 col-sm-5 col-xs-12">
       <input type="text" class="form-control" name="tringno" id="tringno" readonly>
       <input type="hidden" id="tankidcincin" name="tid">
        </div>
      </div>
          
      <div class="col-md-9 col-sm-9 col-xs-12 form-group">
        <label class="control-label col-md-4 col-sm-3 col-xs-12"> Height Start <sup>cm</sup> </label>
        <div class="col-md-5 col-sm-5 col-xs-12">
         <input type="text" class="form-control" name="tstart" id="tstart" required>
        </div>
      </div>
    
      <div class="col-md-9 col-sm-9 col-xs-12 form-group">
        <label class="control-label col-md-4 col-sm-3 col-xs-12"> Height End <sup>cm</sup> </label>
        <div class="col-md-5 col-sm-5 col-xs-12">
         <input type="text" class="form-control" name="tend" id="tend" required>
        </div>
      </div>
    
      <div class="clear"></div> 
      <hr>
      
      <div class="col-md-9 col-sm-9 col-xs-12 form-group">
        <label class="control-label col-md-4 col-sm-3 col-xs-12"> Precision <sup>mm</sup> </label>
        <div class="col-md-5 col-sm-4 col-xs-12">
          <table>
              <tr> <td>
          <select id="cpress_type" class="form-control" style="width:100px;">
              <option value="0"> --</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
          </select> 
              </td> 
<td> <button type="button" class="btn btn-warning" id="bpresisi" style="margin-left:5px;"> Post </button> </td>
              </tr>
          </table>    
         
        </div>
      </div>
    
      <div class="col-md-9 col-sm-9 col-xs-12 form-group">
        <label class="control-label col-md-4 col-sm-3 col-xs-12"> 1 <sup>mm</sup> </label>
        <div class="col-md-4 col-sm-4 col-xs-12">
         <input type="text" class="form-control tpressval" name="t1" id="t1" required readonly>
         <input type="hidden" id="press1">
        </div>
      </div>
    
      <div class="col-md-9 col-sm-9 col-xs-12 form-group">
        <label class="control-label col-md-4 col-sm-3 col-xs-12"> 2 <sup>mm</sup> </label>
        <div class="col-md-4 col-sm-4 col-xs-12">
         <input type="text" class="form-control tpressval" name="t2" id="t2" required readonly>
         <input type="hidden" id="press2">
        </div>
      </div>
    
     <div class="col-md-9 col-sm-9 col-xs-12 form-group">
        <label class="control-label col-md-4 col-sm-3 col-xs-12"> 3 <sup>mm</sup> </label>
        <div class="col-md-4 col-sm-4 col-xs-12">
         <input type="text" class="form-control tpressval" name="t3" id="t3" required readonly>
         <input type="hidden" id="press3">
        </div>
      </div>
    
    <div class="col-md-9 col-sm-9 col-xs-12 form-group">
        <label class="control-label col-md-4 col-sm-3 col-xs-12"> 4 <sup>mm</sup> </label>
        <div class="col-md-4 col-sm-4 col-xs-12">
         <input type="text" class="form-control tpressval" name="t4" id="t4" required readonly>
            <input type="hidden" id="press4">
        </div>
      </div>
    
    <div class="col-md-9 col-sm-9 col-xs-12 form-group">
    <label class="control-label col-md-4 col-sm-3 col-xs-12"> 5 <sup>mm</sup> </label>
    <div class="col-md-4 col-sm-4 col-xs-12">
     <input type="text" class="form-control tpressval" name="t5" id="t5" required readonly>
        <input type="hidden" id="press5">
    </div>
    </div>
    
    <div class="col-md-9 col-sm-9 col-xs-12 form-group">
    <label class="control-label col-md-4 col-sm-3 col-xs-12"> 6 <sup>mm</sup> </label>
    <div class="col-md-4 col-sm-4 col-xs-12">
     <input type="text" class="form-control tpressval" name="t6" id="t6" required readonly>
        <input type="hidden" id="press6">
    </div>
    </div>
    
    <div class="col-md-9 col-sm-9 col-xs-12 form-group">
    <label class="control-label col-md-4 col-sm-3 col-xs-12"> 7 <sup>mm</sup> </label>
    <div class="col-md-4 col-sm-4 col-xs-12">
     <input type="text" class="form-control tpressval" name="t7" id="t7" required readonly>
        <input type="hidden" id="press7">
    </div>
    </div>
    
    <div class="col-md-9 col-sm-9 col-xs-12 form-group">
    <label class="control-label col-md-4 col-sm-3 col-xs-12"> 8 <sup>mm</sup> </label>
    <div class="col-md-4 col-sm-4 col-xs-12">
     <input type="text" class="form-control tpressval" name="t8" id="t8" required readonly>
        <input type="hidden" id="press8">
    </div>
    </div>
    
    <div class="col-md-9 col-sm-9 col-xs-12 form-group">
    <label class="control-label col-md-4 col-sm-3 col-xs-12"> 9 <sup>mm</sup> </label>
    <div class="col-md-4 col-sm-4 col-xs-12">
     <input type="text" class="form-control tpressval" name="t9" id="t9" required readonly>
        <input type="hidden" id="press9">
    </div>
    </div>
    
    <div class="col-md-9 col-sm-9 col-xs-12 form-group">
    <label class="control-label col-md-4 col-sm-3 col-xs-12"> 10 <sup>mm</sup> </label>
    <div class="col-md-4 col-sm-4 col-xs-12">
     <input type="text" class="form-control tpressval" name="t10" id="t10" required readonly>
        <input type="hidden" id="press10">
    </div>
    </div>
    
      <div class="clearfix"></div> 
      
      <div class="ln_solid"></div>
      <div class="form-group">
        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3 btn-group">
          <button type="submit" class="btn btn-primary" id="button">Save</button>
          <button type="button" id="bclose" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="reset" id="bresetcincin" class="btn btn-success">Reset</button>
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