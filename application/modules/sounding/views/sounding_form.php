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

<script src="<?php echo base_url(); ?>js/moduljs/sounding.js"></script>
<script src="<?php echo base_url(); ?>js-old/register.js"></script>

<script type="text/javascript">

	var sites_add  = "<?php echo site_url('sounding/add_process/');?>";
	var sites_edit = "<?php echo site_url('sounding/update_process/');?>";
	var sites_del  = "<?php echo site_url('sounding/delete/');?>";
	var sites_get  = "<?php echo site_url('sounding/update/');?>";
    var sites  = "<?php echo site_url('sounding/');?>";
    var sites_tank  = "<?php echo site_url('tank/details/');?>";
	var source = "<?php echo $source;?>";
	
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
  
  <div id="step-1">
    <!-- form -->
    <form id="ajaxtransform" data-parsley-validate class="form-horizontal form-label-left" method="POST" 
    action="<?php echo $form_action; ?>" >
		
    <style type="text/css">
       .xborder{ border: 1px solid red;}
       #custtitlebox{ height: 160px; background-color: #E0F7FF; border-top: 3px solid #2A3F54; margin-bottom: 10px; }
        #amt{ color: #000; margin-top: 35px; text-align: right; font-weight: bold;}
        #amt span{ color: blue;}
        .labelx{ font-weight: bold; color: #000;}
        #table_summary{ font-size: 12px; color: #000;}
        .amt{ text-align: right;}
    </style>

<!-- form atas   -->
    <div class="row">
       
<!-- div untuk customer place  -->
       <div id="custtitlebox" class="col-md-12 col-sm-12 col-xs-12">
            
           <div class="form-group">
               
               <div class="col-md-3 col-sm-12 col-xs-12">
                   <label class="control-label labelx"> Code - DocNo </label> <br>
     <input type="text" name="tno" id="tno" class="form-control" style="width:190px;" value="<?php echo isset($default['docno']) ? $default['docno'] : '' ?>">   
     <input type="hidden" name="tid" value="<?php echo isset($default['tid']) ? $default['tid'] : '' ?>">
               </div>
               
               <div class="col-md-3 col-sm-12 col-xs-12">
<label class="control-label labelx"> Tank </label>
<table>
    <tr> <td> 
<?php  $js = "class='form-control' id='ctank' tabindex='-1' style='width:120px; float:left; margin-right:5px;' ";
echo form_dropdown('ctank', $tank, isset($default['tank']) ? $default['tank'] : '', $js); ?>        
    </td> 
    <td> <button type="button" id="bfetchtank" class="btn btn-default button_inline"> FETCH </button> </td>
    </tr>
</table>           
               </div>
               
               <div class="col-md-2 col-sm-12 col-xs-12">
                   <label class="control-label labelx"> Transaction Date </label>
           <input type="text" title="Date" class="form-control" id="ds1" name="tdate" required value="<?php echo isset($default['dates']) ? $default['dates'] : '' ?>" /> 
               </div>
               
               <div class="col-md-8 col-sm-12 col-xs-12">
                   <label class="control-label labelx"> Remarks </label>
                   <textarea name="tnote" class="form-control" id="tnote"><?php echo isset($default['notes']) ? $default['notes'] : '' ?></textarea>
               </div>
                              
           </div>
           
       </div>
<!-- div untuk customer place  -->

<!-- div tgl transaksi -->
    <h4> Sounding Result </h4>
    <div class="col-md-12 col-sm-12 col-xs-12">
       
        <style type="text/css">
            .sounds{ width: 130px; margin: 3px;}
            .sounds1{ width: 150px; margin: 3px;}
        </style>
        
        <table>
            <tr>
<td> <label class="control-label labelx"> Sounding (cm) </label> <br> 
<input type="number" name="tsounding" class="form-control sounds" value="<?php echo isset($default['sounding']) ? $default['sounding'] : '0' ?>" id="tincm"> </td>
<td> <label class="control-label labelx"> Corr. (cm) </label> <br> 
<input type="number" name="tcorr" class="form-control sounds" id="tcorcm" value="<?php echo isset($default['corr']) ? $default['corr'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Aft.Corr. (cm) </label> <br>
<input type="text" name="tacorr" id="tacorr" readonly class="form-control sounds" value="<?php echo isset($default['after_corr']) ? $default['after_corr'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Temp (&#8451;) </label> <br>
<input type="number" name="ttemp" class="form-control sounds" id="ttemp" value="<?php echo isset($default['temperature']) ? $default['temperature'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Density </label> <br> 
<input type="text" name="tdensity" readonly class="form-control sounds" id="tdensity" value="<?php echo isset($default['density']) ? $default['density'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Coeff </label> <br> 
<input type="text" name="tcoeff" readonly class="form-control sounds" id="tcoeff" value="<?php echo isset($default['coeff']) ? $default['coeff'] : '0' ?>"> </td>

            </tr>
            
            <tr>
<td> <label class="control-label labelx"> OBV </label> <br> 
<input type="text" name="tobv" id="tobv" readonly class="form-control sounds" value="<?php echo isset($default['obv']) ? $default['obv'] : '0' ?>"> 
<input type="hidden" name="hobv" id="hobv" value="<?php echo isset($default['obv']) ? $default['obv'] : '0' ?>">
</td>
<td> <label class="control-label labelx"> Adj (kg) </label> <br>
<input type="number" id="tadj" name="tadj" class="form-control sounds" value="<?php echo isset($default['adj']) ? $default['adj'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> V.C.V </label> <br> 
<input type="number" name="tvcv" id="tvcv" class="form-control sounds" value="<?php echo isset($default['vcv']) ? $default['vcv'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Table-56 </label> <br>
<input type="text" name="ttable56" id="ttable56" class="form-control sounds" value="0"> </td>
<td> <label class="control-label labelx"> Kg </label> <br> 
<input type="text" name="tnetkg" id="tnetkg" readonly class="form-control sounds1" value="<?php echo isset($default['netkg']) ? $default['netkg'] : '0' ?>"> 
<input type="hidden" name="hnetkg" id="hnetkg" value="<?php echo isset($default['netkg']) ? $default['netkg'] : '0' ?>">
<input type="hidden" name="hbegin" id="hbegin" value="0">
</td>
<td> <label class="control-label labelx"> Metric Ton </label> <br> 
<input type="text" name="tmetricton" id="tmetricton" readonly class="form-control sounds1" value="<?php echo isset($default['metric']) ? $default['metric'] : '0' ?>"> </td>
            </tr>
        </table>
     
     <h4> Lab </h4>    
         <table>
            <tr>
<td> <label class="control-label labelx"> FFA% </label> <br> 
     <input type="text" name="tffa" class="form-control sounds" value="<?php echo isset($default['metric']) ? $default['metric'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Moisture% </label> <br> 
     <input type="text" name="tmoisture" class="form-control sounds" value="<?php echo isset($default['metric']) ? $default['metric'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Dirt% </label> <br> 
     <input type="text" name="tdirt" class="form-control sounds" value="<?php echo isset($default['metric']) ? $default['metric'] : '0' ?>"> </td>
<td> <label class="control-label labelx"> Performed By </label> <br> 
     <input type="text" name="tuser" class="form-control sounds1" value="<?php echo $user; ?>"> </td>
            </tr>

        </table>    
        
    </div>
    
<!-- div tgl transaksi -->

</div>
<!-- form atas   -->
      
      <div class="ln_solid"></div>
      <div class="form-group">
        <div class="col-md-3 col-sm-3 col-xs-12 col-md-offset-9">
          <div class="btn-group">    
          <button type="submit" class="btn btn-success" id="button"> Save </button>
          <button type="reset" class="btn btn-danger" id="breset"> Cancel </button>
          </div>
        </div>
      </div>
      
	</form>
      
    <!-- end div layer 1 -->
        
  </div>
                  
     </div>
       
       <!-- links -->
       <?php if (!empty($link)){foreach($link as $links){echo $links . '';}} ?>
       <!-- links -->
                     
    </div>
  </div>
      
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
    <script type="text/javascript" src="<?php echo base_url(); ?>js/wizard/jquery.smartWizard.js"></script>
        
        <!-- jQuery Smart Wizard -->
    <script type="text/javascript">
      $(document).ready(function() {
        $('#wizard').smartWizard();

        $('#wizard_verticle').smartWizard({
          transitionEffect: 'slide'
        });

      });
    </script>
    <!-- /jQuery Smart Wizard -->
    
    
