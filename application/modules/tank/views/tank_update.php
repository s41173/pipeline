
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

<script src="<?php echo base_url(); ?>js/moduljs/tank.js"></script>
<script src="<?php echo base_url(); ?>js/moduljs/tankapi.js"></script>
<script src="<?php echo base_url(); ?>js-old/register.js"></script>

<script type="text/javascript">

	var sites_add  = "<?php echo site_url('tank/add_process/');?>";
	var sites_edit = "<?php echo site_url('tank/update_process/');?>";
	var sites_del  = "<?php echo site_url('tank/delete/');?>";
	var sites_get  = "<?php echo site_url('tank/update/');?>";
    var sites_density_update  = "<?php echo site_url('tank/density_update/'.$uniqueid);?>";
    var sites_density_remove  = "<?php echo site_url('tank/remove_density/'.$uniqueid);?>";
    
    var sites_calibrate_update  = "<?php echo site_url('tank/calibration_update/'.$uniqueid);?>";
    var sites_calibrate_remove  = "<?php echo site_url('tank/remove_calibration/'.$uniqueid);?>";
    
    var sites_cincin_remove  = "<?php echo site_url('tank/remove_cincin/'.$uniqueid);?>";
    var sites_cincin_update  = "<?php echo site_url('tank/cincin_update/'.$uniqueid);?>";
    var sites_presisi_update  = "<?php echo site_url('tank/update_presisi/'.$uniqueid);?>";
    
	var source = "<?php echo $source;?>";
    var url  = "<?php echo $graph;?>";
    
    var url_calibrate  = "<?php echo site_url('tank/fetch_callibration').'/'.$uniqueid;?>";
    var url_density  = "<?php echo site_url('tank/fetch_density/'.$uniqueid)?>";
    var url_cincin  = "<?php echo site_url('tank/fetch_ring/'.$uniqueid)?>";
	
    load_density();
    
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
        <span class="step_descr"> <small> General </small> </span>
      </a>
    </li>
    <li>
      <a href="#step-2">
        <span class="step_no">2</span>
        <span class="step_descr"> <small> Dimension </small> </span>
      </a>
    </li>
    <li>
      <a href="#step-3">
        <span class="step_no">3</span>
        <span class="step_descr"> <small> Densitas &amp; Masa Jenis </small> </span>
      </a>
    </li>
    <li>
      <a href="#step-4">
        <span class="step_no">4</span>
        <span class="step_descr"> <small> Calibration Table </small> </span>
      </a>
    </li>
    <li>
      <a href="#step-5">
        <span class="step_no">5</span>
        <span class="step_descr"> <small> Cincin </small> </span>
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
      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sku"> API-ID </label>
	  <div class="col-md-3 col-sm-6 col-xs-12">
        <table style="width:100%;">
            <tr> <td> 
           <input type="text" class="form-control" id="tapi" required name="tapi" readonly
           value="<?php echo isset($default['api']) ? $default['api'] : '' ?>">        
            </td>
<!--      <td> <button type="button" id="bfetchapi" class="btn btn-default button_inline"> GET ID </button> </td> -->
</tr>
        </table>  
        
      </div>
      </div>    
        
      <div class="form-group">  
      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sku"> SKU </label>
	  <div class="col-md-3 col-sm-6 col-xs-12">
        <input type="text" class="form-control" id="tsku" required name="tsku" placeholder="SKU" 
        value="<?php echo isset($default['sku']) ? $default['sku'] : '' ?>">
          <input type="hidden" name="tid" value="<?php echo $uid; ?>">
      </div>
      </div>
	             
      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Name </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
  	       <input type="text" title="Product Name" class="form-control" id="tname" name="tname" required placeholder=""
           value="<?php echo isset($default['name']) ? $default['name'] : '' ?>" />
        </div>
      </div>
      
      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Model </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
  	       <input type="text" title="Product Model" class="form-control" id="tmodel" name="tmodel" required placeholder=""
           value="<?php echo isset($default['model']) ? $default['model'] : '' ?>" />
        </div>
      </div>  
        
       <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Minimum Order </label>
        <div class="col-md-2 col-sm-6 col-xs-12">
  	       <input type="text" title="Min Order" class="form-control" id="tmin" name="tmin" placeholder="Min Order"
           value="<?php echo isset($default['min']) ? $default['min'] : '' ?>" />
        </div>
      </div>        
      
  
      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Weight </label>
        <div class="col-md-2 col-sm-6 col-xs-12">
<input type="text" title="Weight" class="form-control" id="tweight" name="tweight" placeholder="Weight (kg)"
           value="<?php echo isset($default['weight']) ? $default['weight'] : '' ?>" />
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
     
    <!-- form -->
<form id="upload_form_update2" data-parsley-validate class="form-horizontal form-label-left" method="POST" 
action="<?php echo $form_action.'/2'; ?>" >

      <div class="form-group">
        <label class="control-label col-md-2 col-sm-2 col-xs-12"> Dimension </label>
        <div class="col-md-3 col-sm-9 col-xs-12">
<input type="text" title="Dimension" class="form-control" id="tdimension" name="tdimension" placeholder="(L x W x H)"
           value="<?php echo isset($default['dimension']) ? $default['dimension'] : '' ?>" />
            <input type="hidden" name="tid" value="<?php echo $uid; ?>">
        </div>
      </div>
    
       <div class="form-group">
        <label class="control-label col-md-2 col-sm-2 col-xs-12"> Tank Height (cm) </label>
        <div class="col-md-3 col-sm-9 col-xs-12">
<input type="text" title="Height" class="form-control" id="theight" name="theight" placeholder="Tank height (cm)"
           value="<?php echo isset($default['height']) ? $default['height'] : '' ?>" />
        </div>
      </div>
    
       <div class="form-group">
        <label class="control-label col-md-2 col-sm-2 col-xs-12"> Measuring Table (cm) </label>
        <div class="col-md-3 col-sm-9 col-xs-12">
<input type="text" title="Measuring" class="form-control" id="tmeasure" name="tmeasure" placeholder="Measuring Table (cm)"
           value="<?php echo isset($default['measuring']) ? $default['measuring'] : '' ?>" />
        </div>
      </div>
    
      <div class="form-group">
        <label class="control-label col-md-2 col-sm-2 col-xs-12"> Standard Temperature </label>
        <div class="col-md-3 col-sm-9 col-xs-12">
<input type="text" title="Temperature" class="form-control" id="ttemperature" name="ttemperature" placeholder="Standard Temperature" value="<?php echo isset($default['temperature']) ? $default['temperature'] : '' ?>" />
        </div>
      </div>
    
      <div class="form-group">
        <label class="control-label col-md-2 col-sm-2 col-xs-12"> Coeff </label>
        <div class="col-md-3 col-sm-9 col-xs-12">
<input type="text" title="Coeff" class="form-control" id="tcoeff" name="tcoeff" placeholder="Coeffisien" value="<?php echo isset($default['coeff']) ? $default['coeff'] : '' ?>" />
        </div>
      </div>
    
      <div class="form-group">
        <label class="control-label col-md-2 col-sm-2 col-xs-12"> Density </label>
        <div class="col-md-3 col-sm-9 col-xs-12">
<input type="text" title="Density" class="form-control" id="tdensity" name="tdensity" placeholder="Density" value="<?php echo isset($default['density']) ? $default['density'] : '' ?>" />
        </div>
      </div>
    
      <div class="form-group">
        <label class="control-label col-md-2 col-sm-2 col-xs-12"> Use Ring </label>
        <div class="col-md-3 col-sm-9 col-xs-12">
<input class="form-control" type="checkbox" name="cring" value="1" <?php echo isset($default['ring']) ? $default['ring'] : '' ?>>
        </div>
      </div>
    
       <div class="form-group">
        <label class="control-label col-md-2 col-sm-2 col-xs-12"> Extra - kg </label>
        <div class="col-md-3 col-sm-9 col-xs-12">
<input type="text" title="Extra Kg" class="form-control" id="textrakg" name="textrakg" placeholder="Extra Kg"
           value="<?php echo isset($default['extrakg']) ? $default['extrakg'] : '' ?>" />
        </div>
      </div>
    
       <div class="form-group">
        <label class="control-label col-md-2 col-sm-2 col-xs-12"> Extra - % </label>
        <div class="col-md-3 col-sm-9 col-xs-12">
<input type="text" title="Extra - %" class="form-control" id="textrapercent" name="textrapercent" placeholder="Extra - %"
           value="<?php echo isset($default['extrapercent']) ? $default['extrapercent'] : '' ?>" />
        </div>
      </div>
      
      <div class="ln_solid"></div>
      <div class="form-group">
        <div class="col-md-3 col-sm-3 col-xs-12 col-md-offset-3">
          <button type="submit" class="btn btn-primary" id="button"> Save Data </button>
        </div>
      </div>
      
	</form>
    <!-- end div layer 2 -->
     
  </div>
  <!-- div 2 -->
  
  <!-- div 3 -->
  <div id="step-3">
      
    <table id="" class="table table-striped jambo_table bulk_action">
       <thead>
           <tr> <th> Temperature (&#8451;) </th> <th> Density (kg/l) </th> <th> Action </th> </tr>
       </thead>  
       <tbody id="table-density">
<!--           <tr> <td> 32.00 </td> <td> 0.92745920 </td> </tr>-->
           
       </tbody>
    </table>  
    <div class="btn-group">
    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#myModal3"> CSV-Import </button>
    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal"> Add Density </button>
    <button type="button" class="btn btn-primary" onclick="load_density();" > Reload Density </button>
    </div>
  </div>
  <!-- div 3 -->
  
  <!-- div 4 -->
  <div id="step-4">
    
    <table id="" class="table table-striped jambo_table bulk_action">
       <thead>
           <tr> <th> Height <sup>cm</sup> </th> <th> Volume <sup>l</sup> </th> <th> Action </th> </tr>
       </thead>  
       <tbody id="table-calibrate">
<!--           <tr> <td> 32.00 </td> <td> 0.92745920 </td> </tr>-->
           
       </tbody>
    </table>  
    <div class="btn-group">
    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#myModal3"> CSV-Import </button>
    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal2"> Add Calibration </button>
    <button type="button" class="btn btn-primary" onclick="load_calibrate();" > Reload Calibration </button>
    </div>
  </div>
  <!-- div 4 -->
    
  <!-- div 5 -->
  <div id="step-5">
    
    <table id="" class="table table-striped jambo_table bulk_action">
       <thead>
           <tr> <th> Cincin </th> <th> CM Akhir </th> <th> 1mm </th> <th> 2mm </th> <th> 3mm </th> <th> 4mm </th> 
                <th> 5mm </th> <th> 6mm </th> <th> 7mm </th> <th> 8mm </th> <th> 9mm </th> <th>10mm</th> 
                <th> Action </th> 
           </tr>
       </thead>  
       <tbody id="table-cincin"> </tbody>
    </table>  
    
    <div class="btn-group">
      <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#myModal3"> CSV-Import </button>
      <button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal5"> Add Cincin </button>
      <button type="button" class="btn btn-primary" onclick="load_cincin();" > Reload Cincin </button>
    </div>  
      
  </div>
  <!-- div 5 -->    
  
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
     <?php $this->load->view('tank_density_form'); ?>      
  </div>
  <!-- Modal - Add Form -->
              
  <!-- Modal - Add Form -->
  <div class="modal fade" id="myModal1" role="dialog">
     <?php $this->load->view('tank_density_update'); ?>      
  </div>
  <!-- Modal - Add Form -->
              
  <!-- Modal - Add Form -->
  <div class="modal fade" id="myModal3" role="dialog">
     <?php $this->load->view('tank_density_import'); ?>     
  </div>
  <!-- Modal - Add Form -->
              
  <!-- Modal - Add Calibration Form -->
  <div class="modal fade" id="myModal2" role="dialog">
     <?php $this->load->view('tank_calibration_form'); ?>      
  </div>
  <!-- Modal - Add Calibration Form -->       
              
   <!-- Modal - Calibration Update -->
  <div class="modal fade" id="myModal4" role="dialog">
     <?php $this->load->view('tank_calibration_update'); ?>      
  </div>
  <!-- Modal - Calibration Update -->   
              
  <!-- Modal - Cincin Form -->
  <div class="modal fade" id="myModal5" role="dialog">
     <?php $this->load->view('tank_cincin_form'); ?>      
  </div>
  <!-- Modal - Cincin Form -->  
              
  <!-- Modal - Cincin Update -->
  <div class="modal fade" id="myModal6" role="dialog">
     <?php $this->load->view('tank_cincin_update'); ?>      
  </div>
  <!-- Modal - Cincin Update --> 
      
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
    
    
