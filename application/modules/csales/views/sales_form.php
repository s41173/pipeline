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

<script src="<?php echo base_url(); ?>js/moduljs/csales.js"></script>
<script src="<?php echo base_url(); ?>js-old/register.js"></script>

<script type="text/javascript">

	var sites_add  = "<?php echo site_url('csales/add_process/');?>";
	var sites_edit = "<?php echo site_url('csales/update_process/');?>";
	var sites_del  = "<?php echo site_url('csales/delete/');?>";
	var sites_get  = "<?php echo site_url('csales/update/');?>";
    var sites  = "<?php echo site_url('csales');?>";
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
                    
<!--
  <div id="errors" class="alert alert-danger alert-dismissible fade in" role="alert"> 
     <?php // $flashmessage = $this->session->flashdata('message'); ?> 
	 <?php // echo ! empty($message) ? $message : '' . ! empty($flashmessage) ? $flashmessage : ''; ?> 
  </div>
-->
  
  <div id="step-1">
    <!-- form -->
    <form id="salesformdata" data-parsley-validate class="form-horizontal form-label-left" method="POST" 
    action="<?php echo $form_action; ?>" 
      enctype="multipart/form-data">
		
    <style type="text/css">
       .xborder{ border: 1px solid red;}
       #custtitlebox{ height: 90px; background-color: #E0F7FF; border-top: 3px solid #2A3F54; margin-bottom: 10px; }
        #amt{ color: #000; margin-top: 35px; text-align: right; font-weight: bold;}
        #amt span{ color: blue;}
        .labelx{ font-weight: bold; color: #000;}
        #table_summary{ font-size: 16px; color: #000;}
        .amt{ text-align: right;}
    </style>

<!-- form atas   -->
    <div class="row">
       
<!-- div untuk customer place  -->
       <div id="custtitlebox" class="col-md-12 col-sm-12 col-xs-12">
            
           <div class="form-group">
               
               <div class="col-md-2 col-sm-12 col-xs-12">
                   <label class="control-label labelx"> Contract </label>
<?php $js = "class='select2_single form-control' id='ccontract' tabindex='-1' style='min-width:150px;' "; 
echo form_dropdown('ccontract', $contract, isset($default['contract']) ? $default['contract'] : '', $js); ?>
               </div>
                
           </div>
           
       </div>
<!-- div untuk customer place  -->

<!-- div alamat penagihan -->
       <div class="col-md-3 col-sm-12 col-xs-12">
           <div class="col-md-12 col-sm-12 col-xs-12">
              <label class="control-label labelx"> Note </label>
      <textarea name="tnote" style="width:100%;" rows="4"><?php echo isset($default['note']) ? $default['note'] : '' ?></textarea>
           </div>
       </div>
<!-- div alamat penagihan -->

<!-- div tgl transaksi -->
    <div class="col-md-2 col-sm-12 col-xs-12">
       
       <div class="col-md-12 col-sm-12 col-xs-12">
          <label class="control-label labelx"> Transaction Date </label>
          <input type="text" title="Date" class="form-control" id="ds1" name="tdates" required
           value="<?php echo isset($default['dates']) ? $default['dates'] : '' ?>" /> 
       </div>
        
        <!-- due date    -->
       <div class="col-md-12 col-sm-12 col-xs-12">
          <label class="control-label labelx"> Due Date </label>
          <input type="text" title="Due Date" class="form-control" id="ds2" name="tduedates" required 
           value="<?php echo isset($default['due_date']) ? $default['due_date'] : '' ?>" /> 
       </div>    
        
    </div>
<!-- div tgl transaksi -->
        
<!-- div no transaksi -->
  <div class="col-md-2 col-sm-12 col-xs-12">
       
      <div class="col-md-12 col-sm-12 col-xs-12">
          <label class="control-label labelx"> Trans Code </label>
          <input type="text" title="Trans Code" class="form-control" readonly name="tcode" value="SO-0<?php echo $counter; ?>" /> 
       </div>  
      
      <div class="col-md-12 col-sm-12 col-xs-12">
          <label class="control-label labelx"> Payment Type </label>
          <?php $js = "class='form-control' id='cpayment' tabindex='-1' style='min-width:150px;' "; 
	      echo form_dropdown('cpayment', $payment, isset($default['payment']) ? $default['payment'] : '', $js); ?>
      </div>  
        
  </div>
<!-- div no transaksi -->
        
<!-- div landed cost -->
  <div class="col-md-2 col-sm-12 col-xs-12">
      
      <div class="col-md-12 col-sm-12 col-xs-12">
          <label class="control-label labelx"> Amount </label>
          <input type="number" title="Amount" class="form-control" name="tamount" id="tamount" value="<?php echo isset($default['amount']) ? $default['amount'] : '' ?>" /> 
      </div>  
      
      <div class="col-md-12 col-sm-12 col-xs-12"></div>
        
  </div>
<!-- div landed cost -->
        
<!-- div down payment -->
  <div class="col-md-2 col-sm-12 col-xs-12">
      <div class="col-md-12 col-sm-12 col-xs-12">
          <label class="control-label labelx"> Tax </label>
           <?php $js = "class='form-control' id='ctax' tabindex='-1' style='min-width:100px;' "; 
	       echo form_dropdown('ctax', $tax, isset($default['tax']) ? $default['tax'] : '', $js); ?>
      </div>
      
      <div class="col-md-12 col-sm-12 col-xs-12">
          <label class="control-label labelx"> Tax Amount </label>
          <input type="number" title="Tax Amount" class="form-control" id="ttax" name="ttax" readonly value="<?php echo isset($default['taxval']) ? $default['taxval'] : '' ?>" /> 
      </div>
  </div>
<!-- div down payment -->

</div>
<!-- form atas   -->
      
      <div class="ln_solid"></div>
      <div class="form-group">
        <div class="col-md-4 col-sm-4 col-xs-12 col-md-offset-9">
          <div class="btn-group">    
          <button type="submit" class="btn btn-success" id="button"> Save </button>
          <button type="reset" class="btn btn-danger" id=""> Cancel </button>
          <a class="btn btn-primary" href="<?php echo site_url('sales/add/'); ?>"> New Transaction </a> 
          </div>
        </div>
      </div>
      
	</form>
      
    <!-- end div layer 1 -->
      
<!-- form transaction table  -->
 
                    
<?php
                        
$atts2 = array(
	  'class'      => 'btn btn-primary button_inline',
	  'title'      => 'Product',
	  'width'      => '800',
	  'height'     => '600',
	  'scrollbars' => 'yes',
	  'status'     => 'yes',
	  'resizable'  => 'yes',
	  'screenx'    =>  '\'+((parseInt(screen.width) - 800)/2)+\'',
	  'screeny'    =>  '\'+((parseInt(screen.height) - 600)/2)+\'',
);

?>      
      
<div class="row">
    
    
<!-- kolom total -->
    <div class="col-md-3 col-sm-12 col-xs-12 col-md-offset-9">
        
        <table id="table_summary" style="width:100%;">
<tr> <td> Sub Total </td> <td class="amt"> <?php echo isset($tot_amt) ? idr_format($tot_amt) : '0'; ?>,- </td> </tr>
<tr> <td> Discount (-) </td> <td class="amt"> <?php echo isset($discount) ? idr_format($discount) : '0'; ?>,- </td> </tr>
<tr> <td> Tax </td> <td class="amt"> <?php echo isset($tax_total) ? idr_format($tax_total) : '0' ?>,- </td> </tr>
<tr> <td> Down Payment (-) </td> <td class="amt"> <?php echo isset($p1) ? idr_format($p1) : '0' ?>,- </td> </tr>
<tr> <td> <h3 style="color:#337AB7; font-weight:bold;"> Total </h3> </td> 
     <td class="amt"> <h3 style="color:#337AB7; font-weight:bold;"> <?php echo isset($total) ? idr_format($total) : '0' ?>,- </h3> </td> </tr>
        </table>
        
    </div>
<!-- kolom total -->
    
</div>
<!-- form transaction table  -->  
        
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
    
    
