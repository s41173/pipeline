
 <!-- Datatables CSS -->
<link href="<?php echo base_url(); ?>js/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/dataTables.tableTools.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>css/icheck/flat/green.css" rel="stylesheet" type="text/css">

<!-- Date time picker -->
 <script type="text/javascript" src="http://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
 
 <!-- Include Date Range Picker -->
<script type="text/javascript" src="http://cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="http://cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />


<style type="text/css">
  a:hover { text-decoration:none;}
</style>

<script src="<?php echo base_url(); ?>js/moduljs/shorec.js"></script>
<script src="<?php echo base_url(); ?>js-old/register.js"></script>

<script type="text/javascript">

	var sites_add  = "<?php echo site_url('shorec/add_process/');?>";
	var sites_edit = "<?php echo site_url('shorec/update_process/');?>";
	var sites_del  = "<?php echo site_url('shorec/delete/');?>";
	var sites_get  = "<?php echo site_url('shorec/update/');?>";
    var sites  = "<?php echo site_url('shorec/');?>";
    var sites_sounding  = "<?php echo site_url('sounding/');?>";
    
	var source = "<?php echo $source;?>";
    var url  = "<?php echo $graph;?>";

</script>

          <div class="row"> 
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="x_panel" >
              
              <!-- xtitle -->
              <div class="x_title">
              
                <ul class="nav navbar-right panel_toolbox">
                  <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                  <li><a class="close-link"><i class="fa fa-close"></i></a> </li>
                </ul>
                
                <div class="clearfix"></div>
              </div>
              <!-- xtitle -->
                
                <div class="x_content">
                      
                  
             <!-- Smart Wizard -->
<div id="wizard" class="form_wizard wizard_horizontal">
                     <!-- error div -->
 <div class="alert alert-success success"> </div>
 <div class="alert alert-warning warning"> </div>
 <div class="alert alert-error error"> </div>
    
    
  <ul class="wizard_steps">
    <li>
      <a href="#step-1">
        <span class="step_no">1</span>
        <span class="step_descr"> <small> Detail </small> </span>
      </a>
    </li>
    <li>
      <a href="#step-2">
        <span class="step_no">2</span>
        <span class="step_descr"> <small> Calculation </small> </span>
      </a>
    </li>
    <li>
      <a href="#step-3">
        <span class="step_no">3</span>
        <span class="step_descr"> <small> Execution </small> </span>
      </a>
    </li>
   
  </ul>
  
<!--
  <div id="errors" class="alert alert-danger alert-dismissible fade in" role="alert"> 
     <?php // $flashmessage = $this->session->flashdata('message'); ?> 
	 <?php // echo ! empty($message) ? $message : '' . ! empty($flashmessage) ? $flashmessage : ''; ?> 
  </div>
-->
  
  <div id="step-1">
    <!-- form -->
    <form id="upload_form_update" data-parsley-validate class="form-horizontal form-label-left" method="POST" 
    action="<?php echo $form_action.'/1'; ?>" 
      enctype="multipart/form-data">
		
      <div class="form-group">  
      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sku"> Docno </label>
	  <div class="col-md-4 col-sm-6 col-xs-12">
        <table style="width:100%;">
            <tr> <td> 
<input type="text" class="form-control" required name="tno" value="<?php echo isset($default['docno']) ? $default['docno'] : '' ?>">       
<input type="hidden" name="tid" value="<?php echo $uid; ?>">
            </td>
           </tr>
        </table>  
        
      </div>
      </div>    
        
      <div class="form-group">  
      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sku"> Trans Date </label>
	  <div class="col-md-2 col-sm-6 col-xs-12">
        <input type="text" class="form-control" id="ds3" required name="tdate" 
        value="<?php echo isset($default['dates']) ? $default['dates'] : '' ?>">
      </div>
      </div>
	             
      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Remarks </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
<textarea name="tnote" rows="3" class="form-control"><?php echo isset($default['notes']) ? $default['notes'] : '' ?></textarea>
        </div>
      </div>
      
      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Type </label>
        <div class="col-md-2 col-sm-6 col-xs-12">
<select name="ctype" id="ctype" class="form-control" style="min-width:120px;">
<option value="nol"> --Select-- </option>
<option value="0"<?php echo set_select('ctype', '0', isset($default['type']) && $default['type'] == '0' ? TRUE : FALSE); ?>> Intertank </option>
<option value="1"<?php echo set_select('ctype', '1', isset($default['type']) && $default['type'] == '1' ? TRUE : FALSE); ?>> Ship Outward </option>
<option value="2"<?php echo set_select('ctype', '2', isset($default['type']) && $default['type'] == '2' ? TRUE : FALSE); ?>> Ship Inward </option>
<option value="3"<?php echo set_select('ctype', '3', isset($default['type']) && $default['type'] == '3' ? TRUE : FALSE); ?>> Outward-3-Party </option>
</select>
        </div>
      </div>  
        
       <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Fuel </label>
        <div class="col-md-2 col-sm-6 col-xs-12">
  	       <input class="form-control" type="checkbox" name="cfuel" value="1" <?php echo isset($default['fuel']) ? $default['fuel'] : '' ?>>
        </div>
      </div>        
              
      
      <div class="ln_solid"></div>
      <div class="form-group">
        <div class="col-md-3 col-sm-3 col-xs-12 col-md-offset-3">
          <button type="submit" class="btn btn-primary" id="button"> Save General </button>
        </div>
      </div>
      
	</form>
    <!-- end div layer 1 -->
  </div>
    
  
  <!-- div 2 -->
  <div id="step-2">
 
      <style type="text/css">
          .border{ border: 1px solid red;}
          .dtable{ border-collapse: separate; border-spacing: 3px; }
          .ck{ float: left; margin: 1px; padding: 0;}
      </style>
      
<div class="col-md-4 col-sm-6 col-xs-12">

    <!-- form -->
<form id="upload_form_update2" data-parsley-validate class="form-horizontal form-label-left" method="POST" 
action="<?php echo $form_action.'/2'; ?>" >
      
<table class="dtable">
    
    <tr>
        <td> <label class="control-label"> Vessel : </label> </td> <td></td>
        <td> 
<?php  $js = "class='form-control' id='cvessel' tabindex='-1' disabled='true' style='width:120px; float:left; margin-right:5px;' ";
echo form_dropdown('cvessel', $tank, isset($default['vessel']) ? $default['vessel'] : '', $js); ?>  
<input type="hidden" name="tid" value="<?php echo $uid; ?>">        
</td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> Shipper : </label> </td> <td></td>
        <td> <input type="text" class="form-control" name="tshipper"
           value="<?php echo isset($default['shipper']) ? $default['shipper'] : '' ?>" /> </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> Consignee : </label> </td> <td></td>
<td> <?php  $js = "class='form-control select2_single' id='ccust' tabindex='-1' style='width:170px; float:left; margin-right:5px;' ";
echo form_dropdown('ccust', $customer, isset($default['cust']) ? $default['cust'] : '', $js); ?>  </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> Commodity : </label> </td> <td></td>
        <td> <input type="text" class="form-control" name="tcontent" id="tcontent" readonly value="<?php echo isset($default['content']) ? $default['content'] : '' ?>" /> </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> SI No. : </label> </td> <td></td>
        <td> <input type="text" class="form-control" name="tinstruction"
           value="<?php echo isset($default['instructionno']) ? $default['instructionno'] : '' ?>" /> </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> T.Source : </label> </td> <td></td>
        <td> 
<?php  $js = "class='form-control' id='csource' tabindex='-1' style='width:120px; float:left; margin-right:5px;' ";
echo form_dropdown('csource', $tank, isset($default['source']) ? $default['source'] : '', $js); ?>  </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> ETA : </label> </td> 
<td> <input class="ck" type="checkbox" id="ck1" onclick="set_empty('ck1','dtime1')" checked> </td>
        <td> 
<input type="text" class="form-control" name="teta" id="dtime1"
value="<?php echo isset($default['eta']) ? $default['eta'] : '' ?>" /> </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> ETB : </label> </td>
<td> <input class="ck" type="checkbox" id="ck2" onclick="set_empty('ck2','dtime2')" checked > </td>
        <td> <input type="text" class="form-control" name="tetb" id="dtime2"
           value="<?php echo isset($default['etb']) ? $default['etb'] : '' ?>" /> </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> Laycan Date : </label> </td>
<td> <input class="ck" type="checkbox" id="ck3" onclick="set_empty('ck3','dtime3')" checked > </td>
        <td> <input type="text" class="form-control" name="tlaycan" id="dtime3"
           value="<?php echo isset($default['laycan']) ? $default['laycan'] : '' ?>" /> </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> Until : </label> </td>
<td> <input class="ck" type="checkbox" id="ck4" onclick="set_empty('ck4','dtime4')" checked > </td>
        <td> <input type="text" class="form-control" name="tuntil" id="dtime4"
           value="<?php echo isset($default['until']) ? $default['until'] : '' ?>" /> </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> Heating Date : </label> </td>
<td> <input class="ck" type="checkbox" id="ck5" onclick="set_empty('ck5','dtime5')" checked > </td>
        <td> <input type="text" class="form-control" name="theating" id="dtime5"
           value="<?php echo isset($default['heating']) ? $default['heating'] : '' ?>" /> </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> Heating Until : </label> </td>
<td> <input class="ck" type="checkbox" id="ck6" onclick="set_empty('ck6','dtime6')" checked > </td>
        <td> <input type="text" class="form-control" name="theating_until" id="dtime6"
           value="<?php echo isset($default['heating_until']) ? $default['heating_until'] : '' ?>" /> </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> </label> </td>
<td> <input class="form-control" type="checkbox" name="cboom" value="1" <?php echo isset($default['oil_boom']) ? $default['oil_boom'] : '' ?>> </td>
        <td> <span> Oil Boom </span> </td>
    </tr>
    
</table>
    
      <div class="ln_solid"></div>
      <div class="form-group">
        <div class="col-md-3 col-sm-3 col-xs-12 col-md-offset-3">
          <button type="submit" class="btn btn-primary" id="button"> Save Data </button>
        </div>
      </div>
      
</form>

</div>      
      
<!-- div 2 kanan -->
<div class="col-md-8 col-sm-6 col-xs-12">
  
<form id="soundingform" method="post" action="<?php echo site_url('shorec/add_shore_trans/'.$uid); ?>">
<div class="btn-group" style="float:right;"> <br>
<button type="button" id="bfetchtank" class="btn btn-primary button_inline"> Fetching </button>
<button type="submit" id="bsubmitsounding" class="btn btn-success button_inline" disabled> Save </button>
<button type="reset" id="breset" onClick="" class="btn btn-danger button_inline"> Reset </button>
</div> <div class="clear"></div>
    
<!-- Before -->
<fieldset> <legend> Before </legend>
    <style type="text/css">
            .sounds{ width: 100px; margin: 1px;}
            .sounds1{ width: 110px; margin: 1px;}
    </style>
        
        <table>
            <tr>
<td> <label class="control-label labelx"> Sounding (cm) </label> <br> 
<input type="number" name="tincm_input" class="form-control sounds" id="tincm_input" value="<?php echo isset($default['tincm_input']) ? $default['tincm_input'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Corr. (cm) </label> <br> 
<input type="number" name="tcorcm_input" class="form-control sounds" id="tcorcm_input" value="<?php echo isset($default['tcorcm_input']) ? $default['tcorcm_input'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Aft.Corr. (cm) </label> <br>
<input type="text" name="tacorr_input" id="tacorr_input" readonly class="form-control sounds" value="<?php echo isset($default['tacorr_input']) ? $default['tacorr_input'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Temp (&#8451;) </label> <br>
<input type="number" name="ttemp_input" class="form-control sounds" id="ttemp_input" value="<?php echo isset($default['ttemp_input']) ? $default['ttemp_input'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Density </label> <br> 
<input type="text" name="tdensity_input" readonly class="form-control sounds" id="tdensity_input" value="<?php echo isset($default['tdensity_input']) ? $default['tdensity_input'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Coeff </label> <br> 
<input type="text" name="tcoeff_input" readonly class="form-control sounds" id="tcoeff_input" value="<?php echo isset($default['tcoeff_input']) ? $default['tcoeff_input'] : '0' ?>"> </td>

            </tr>
            
            <tr>
<td> <label class="control-label labelx"> OBV </label> <br> 
<input type="text" name="tobv_input" id="tobv_input" readonly class="form-control sounds" value="<?php echo isset($default['tobv_input']) ? $default['tobv_input'] : '0' ?>"> 
<input type="hidden" name="hobv_input" id="hobv_input" value="<?php echo isset($default['tobv_input']) ? $default['tobv_input'] : '0' ?>">
</td>
<td> <label class="control-label labelx"> Adj (kg) </label> <br>
<input type="number" name="tadj_input" id="tadj_input" class="form-control sounds" value="<?php echo isset($default['tadj_input']) ? $default['tadj_input'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> V.C.V </label> <br> 
<input type="number" name="tvcv_input" id="tvcv_input" class="form-control sounds" value="<?php echo isset($default['tvcv_input']) ? $default['tvcv_input'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Table-56 </label> <br>
<input type="text" name="ttable56_input" id="ttable56_input" class="form-control sounds" value="<?php echo isset($default['ttable56_input']) ? $default['ttable56_input'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Kg </label> <br> 
<input type="text" name="tnetkg_input" id="tnetkg_input" readonly class="form-control sounds1" value="<?php echo isset($default['tnetkg_input']) ? $default['tnetkg_input'] : '0' ?>"> 
<input type="hidden" name="hnetkg_input" id="hnetkg_input" value="<?php echo isset($default['tnetkg_input']) ? $default['tnetkg_input'] : '0' ?>">
<input type="hidden" name="hbegin_input" id="hbegin_input" value="<?php echo isset($default['tnetkg_input']) ? $default['tnetkg_input'] : '0' ?>">
</td>
<td> <label class="control-label labelx"> Metric Ton </label> <br> 
<input type="text" name="tmetricton_input" id="tmetricton_input" readonly class="form-control sounds1"> </td>
            </tr>
        </table>
     
     <h4> Lab </h4>    
         <table>
            <tr>
<td> <label class="control-label labelx"> FFA% </label> <br> 
     <input type="text" name="tffa_input" class="form-control sounds" value="<?php echo isset($default['tffa_input']) ? $default['tffa_input'] : '0' ?>"> 
</td>
<td> <label class="control-label labelx"> Moisture% </label> <br> 
     <input type="text" name="tmoisture_input" class="form-control sounds" value="<?php echo isset($default['tmoisture_input']) ? $default['tmoisture_input'] : '0' ?>"> 
</td>
<td> <label class="control-label labelx"> Dirt% </label> <br> 
    <input type="text" name="tdirt_input" class="form-control sounds" value="<?php echo isset($default['tdirt_input']) ? $default['tdirt_input'] : '0' ?>"> 
</td>
            </tr>
        </table>    
</fieldset>    
   <br> 
<!-- After -->
<fieldset> <legend> After </legend>
    <style type="text/css">
            .sounds{ width: 100px; margin: 1px;}
            .sounds1{ width: 110px; margin: 1px;}
    </style>
        
        <table>
            <tr>
<td> <label class="control-label labelx"> Sounding (cm) </label> <br> 
<input type="number" name="tincm_output" class="form-control sounds" id="tincm_output" value="<?php echo isset($default['tincm_output']) ? $default['tincm_output'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Corr. (cm) </label> <br> 
<input type="number" name="tcorcm_output" class="form-control sounds" id="tcorcm_output" value="<?php echo isset($default['tcorcm_output']) ? $default['tcorcm_output'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Aft.Corr. (cm) </label> <br>
<input type="text" name="tacorr_output" id="tacorr_output" readonly class="form-control sounds" value="<?php echo isset($default['tacorr_output']) ? $default['tacorr_output'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Temp (&#8451;) </label> <br>
<input type="number" name="ttemp_output" class="form-control sounds" id="ttemp_output" value="<?php echo isset($default['ttemp_output']) ? $default['ttemp_output'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Density </label> <br> 
<input type="text" name="tdensity_output" readonly class="form-control sounds" id="tdensity_output" value="<?php echo isset($default['tdensity_output']) ? $default['tdensity_output'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Coeff </label> <br> 
<input type="text" name="tcoeff_output" readonly class="form-control sounds" id="tcoeff_output" value="<?php echo isset($default['tcoeff_output']) ? $default['tcoeff_output'] : '0' ?>"> </td>

            </tr>
            
            <tr>
<td> <label class="control-label labelx"> OBV </label> <br> 
<input type="text" name="tobv_output" id="tobv_output" readonly class="form-control sounds" value="<?php echo isset($default['tobv_output']) ? $default['tobv_output'] : '0' ?>"> 
<input type="hidden" name="hobv_output" id="hobv_output" value="<?php echo isset($default['tobv_output']) ? $default['tobv_output'] : '0' ?>">
</td>
<td> <label class="control-label labelx"> Adj (kg) </label> <br>
<input type="number" name="tadj_output" id="tadj_output" class="form-control sounds" value="<?php echo isset($default['tadj_output']) ? $default['tadj_output'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> V.C.V </label> <br> 
<input type="number" name="tvcv_output" id="tvcv_output" class="form-control sounds" value="<?php echo isset($default['tvcv_output']) ? $default['tvcv_output'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Table-56 </label> <br>
<input type="text" name="ttable56_output" id="ttable56_output" class="form-control sounds" value="<?php echo isset($default['ttable56_output']) ? $default['ttable56_output'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Kg </label> <br> 
<input type="text" name="tnetkg_output" id="tnetkg_output" readonly class="form-control sounds1" value="<?php echo isset($default['tnetkg_output']) ? $default['tnetkg_output'] : '0' ?>"> 
<input type="hidden" name="hnetkg_output" id="hnetkg_output" value="<?php echo isset($default['tnetkg_output']) ? $default['tnetkg_output'] : '0' ?>">
<input type="hidden" name="hbegin_output" id="hbegin_output" value="<?php echo isset($default['tnetkg_output']) ? $default['tnetkg_output'] : '0' ?>">
</td>
<td> <label class="control-label labelx"> Metric Ton </label> <br> 
<input type="text" name="tmetricton_output" id="tmetricton_output" readonly class="form-control sounds1"> </td>
            </tr>
        </table>
     
     <h4> Lab </h4>    
         <table>
            <tr>
<td> <label class="control-label labelx"> FFA% </label> <br> 
     <input type="text" name="tffa_output" class="form-control sounds" value="<?php echo isset($default['tffa_output']) ? $default['tffa_output'] : '0' ?>"> 
</td>
<td> <label class="control-label labelx"> Moisture% </label> <br> 
     <input type="text" name="tmoisture_output" class="form-control sounds" value="<?php echo isset($default['tmoisture_output']) ? $default['tmoisture_output'] : '0' ?>"> 
</td>
<td> <label class="control-label labelx"> Dirt% </label> <br> 
    <input type="text" name="tdirt_output" class="form-control sounds" value="<?php echo isset($default['tdirt_output']) ? $default['tdirt_output'] : '0' ?>"> 
</td>
            </tr>
        </table>     
</fieldset> 
 
</form>        
</div>
      
    <!-- end div layer 2 -->
     
  </div>
  <!-- div 2 -->
  
  <!-- div 3 -->
  <div id="step-3">

<form id="upload_form_update3" data-parsley-validate class="form-horizontal form-label-left" method="POST" 
action="<?php echo $form_action.'/3'; ?>" >  
      
<div class="col-md-5 col-sm-6 col-xs-12"> 
  
<table class="dtable" border="0">
    
     <tr>
        <td> <label class="control-label"> Comm. Pumping : </label> </td>
<td> <input class="ck" type="checkbox" id="ck7" onclick="set_empty('ck7','dtime7')" checked > </td>
        <td> <input type="text" class="form-control" name="tcomm_pumping" id="dtime7"
           value="<?php echo isset($default['comm_pumping']) ? $default['comm_pumping'] : '' ?>" /> 
            <input type="hidden" name="tid" value="<?php echo $uid; ?>">   
        </td>
    </tr>
    
     <tr>
        <td> <label class="control-label"> Comp. Pumping : </label> </td>
<td> <input class="ck" type="checkbox" id="ck8" onclick="set_empty('ck8','dtime8')" checked > </td>
        <td> <input type="text" class="form-control" name="tcomp_pumping" id="dtime8"
           value="<?php echo isset($default['comp_pumping']) ? $default['comp_pumping'] : '' ?>" /> </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> Shore Line Condition : </label> </td>
        <td colspan="2">  
<input type="radio" name="rlinecondition" value="SINGLE" 
<?php echo isset($shore_line_cond_1) ? $shore_line_cond_1 : ''; ?>>
        <span> Single </span>  
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
        <input type="radio" name="rlinecondition" value="MULTI"
<?php echo isset($shore_line_cond_2) ? $shore_line_cond_2 : ''; ?>>
        <span> Multi </span>
        </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> Before Loading : </label> </td>
        <td colspan="2">  
<input type="radio" name="rbeforeload" value="EMPTY" 
<?php echo isset($before_load_1) ? $before_load_1 : ''; ?>>
        <span> Empty </span>    
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
<input type="radio" name="rbeforeload" value="FULL" 
<?php echo isset($before_load_2) ? $before_load_2 : ''; ?>>
        <span> Full </span>
        </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> Cleaning System : </label> </td>
        <td colspan="2">  
<input type="radio" name="rcleaning" value="AIR" 
<?php echo isset($cleaning_sys_1) ? $cleaning_sys_1 : ''; ?>>
        <span> Air Blowing </span>   
        &nbsp;
<input type="radio" name="rcleaning" value="PIGGING"
<?php echo isset($cleaning_sys_2) ? $cleaning_sys_2 : ''; ?>>
        <span> Pigging </span>
        </td> 
    </tr>
    
     <tr>
        <td> <label class="control-label"> After Loading : </label> </td>
        <td colspan="2">  
<input type="radio" name="rafterload" value="EMPTY" 
<?php echo isset($after_load_1) ? $after_load_1 : ''; ?>>
        <span> Empty </span>    
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
<input type="radio" name="rafterload" value="FULL"
<?php echo isset($after_load_2) ? $after_load_2 : ''; ?>>
        <span> Full </span>
        </td>
    </tr>
</table>
        
</div>
      
<!-- kolom kanan -->
<div class="col-md-7 col-sm-6 col-xs-12"> 
    
  <table class="dtable" border="0" style="width:100%;">
    
    <tr>
        <td> <label class="control-label"> Shp.Name : </label> </td> 
        <td> 
<input type="text" class="form-control" name="tshipname"
value="<?php echo isset($default['ship_name']) ? $default['ship_name'] : '' ?>" /> </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> Shp.Rep : </label> </td> 
        <td> 
<input type="text" class="form-control" name="tshiprep"
value="<?php echo isset($default['ship_rep']) ? $default['ship_rep'] : '' ?>" /> </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> Shp Company : </label> </td>
         <td> 
<input type="text" class="form-control" name="tshipcompany"
value="<?php echo isset($default['ship_company']) ? $default['ship_company'] : '' ?>" /> </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> Buyer Name : </label> </td>
        <td> 
<input type="text" class="form-control" name="tbuyername"
value="<?php echo isset($default['buyer_name']) ? $default['buyer_name'] : '' ?>" /> </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> Buyer Rep : </label> </td>
        <td> 
<input type="text" class="form-control" name="tbuyerrep"
value="<?php echo isset($default['buyer_rep']) ? $default['buyer_rep'] : '' ?>" /> </td>
    </tr>
    
    <tr>
        <td> <label class="control-label"> Buyer Company : </label> </td>
        <td> 
<input type="text" class="form-control" name="tbuyercompany"
value="<?php echo isset($default['buyer_company']) ? $default['buyer_company'] : '' ?>" /> </td>
    </tr>
</table>  
    
</div>
 
  <div class="ln_solid"></div>
  <div class="form-group">
    <div class="col-md-3 col-sm-3 col-xs-12 col-md-offset-3">
      <button type="submit" class="btn btn-primary" id="button"> Save Data </button>
    </div>
  </div>
    
</form>

  </div>
  <!-- div 3 -->   
  
</div>
<!-- End SmartWizard Content -->
                      
     </div>
       
       <!-- links -->
       <?php if (!empty($link)){foreach($link as $links){echo $links . '';}} ?>
       <!-- links -->
                     
    </div>
  </div>
              
   <!-- Modal - Add Form -->
  <div class="modal fade" id="myModal" role="dialog">
     <?php //$this->load->view('tank_density_form'); ?>      
  </div>
  <!-- Modal - Add Form -->
      
      <script src="<?php echo base_url(); ?>js/icheck/icheck.min.js"></script>
      
       <!-- Datatables JS -->
        <script src="<?php echo base_url(); ?>js/datatables/jquery.dataTables.min.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/dataTables.bootstrap.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/jszip.min.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/pdfmake.min.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/vfs_fonts.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/dataTables.fixedHeader.min.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/dataTables.keyTable.min.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/dataTables.responsive.min.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/responsive.bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/dataTables.scroller.min.js"></script>
        <script src="<?php echo base_url(); ?>js/datatables/dataTables.tableTools.js"></script>
    
    <!-- jQuery Smart Wizard -->
    <script src="<?php echo base_url(); ?>js/wizard/jquery.smartWizard.js"></script>
        
        <!-- jQuery Smart Wizard -->
    <script>
      $(document).ready(function() {
        $('#wizard').smartWizard();

        $('#wizard_verticle').smartWizard({
          transitionEffect: 'slide'
        });

      });
    </script>
    <!-- /jQuery Smart Wizard -->
    