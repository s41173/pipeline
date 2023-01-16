
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

<script src="<?php echo base_url(); ?>js/moduljs/contract.js"></script>
<script src="<?php echo base_url(); ?>js-old/register.js"></script>

<script type="text/javascript">

	var sites_add  = "<?php echo site_url('shorec/add_process/');?>";
	var sites_edit = "<?php echo site_url('shorec/update_process/');?>";
	var sites_del  = "<?php echo site_url('shorec/delete/');?>";
	var sites_get  = "<?php echo site_url('shorec/update/');?>";
    
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
                  
                     <!-- error div -->
 <div class="alert alert-success success"> </div>
 <div class="alert alert-warning warning"> </div>
 <div class="alert alert-error error"> </div>
    
      
<!--
  <div id="errors" class="alert alert-danger alert-dismissible fade in" role="alert"> 
     <?php // $flashmessage = $this->session->flashdata('message'); ?> 
	 <?php // echo ! empty($message) ? $message : '' . ! empty($flashmessage) ? $flashmessage : ''; ?> 
  </div>
-->
  
    <!-- form -->
    <form id="upload_form_update" data-parsley-validate class="form-horizontal form-label-left" method="POST" 
    action="<?php echo $form_action; ?>" 
      enctype="multipart/form-data">
		
      <div class="form-group">  
      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sku"> Doc-No </label>
	  <div class="col-md-4 col-sm-6 col-xs-12">
<input type="text" class="form-control" required name="tdocno" value="<?php echo isset($default['docno']) ? $default['docno'] : '' ?>">       
<input type="hidden" name="tid" value="<?php echo $uid; ?>">
        
      </div>
      </div>    
        
      <div class="form-group">  
      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sku"> Trans Date </label>
	  <div class="col-md-2 col-sm-6 col-xs-12">
        <input type="text" class="form-control" id="ds3" required name="tdates" placeholder="SKU" 
        value="<?php echo isset($default['date']) ? $default['date'] : '' ?>">
      </div>
      </div>
          
      <div class="form-group">  
      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sku"> Customer </label>
	  <div class="col-md-4 col-sm-6 col-xs-12">
<?php $js = "class='form-control select2_single' id='' tabindex='-1' style='width:250px;' "; 
 echo form_dropdown('ccust', $customer, isset($default['cust']) ? $default['cust'] : '', $js); ?>
        
      </div>
      </div>  
        
      <div class="form-group">  
      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sku"> Period </label>
	  <div class="col-md-4 col-sm-6 col-xs-12">
<table>
    <tr> 
<td> <input type="text" title="Start Date" class="form-control" id="ds2" name="tstart" style="width:100px;" value="<?php echo isset($default['start']) ? $default['start'] : '' ?>" /> </td>
<td> &nbsp; - &nbsp; </td>
<td> <input type="text" title="End Date" class="form-control" id="ds3" name="tend" style="width:100px;" value="<?php echo isset($default['end']) ? $default['end'] : '' ?>" />  </td>
    </tr>
</table>
        
      </div>
      </div>       
        
      <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-12"> Remarks </label>
        <div class="col-md-6 col-sm-6 col-xs-12">
<textarea name="tnote" rows="3" class="form-control"><?php echo isset($default['note']) ? $default['note'] : '' ?></textarea>
        </div>
      </div>
        
     <div class="form-group">  
      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sku"> Amount </label>
	  <div class="col-md-2 col-sm-6 col-xs-12">
<input type="number" class="form-control" required name="tamount" id="tamount" value="<?php echo isset($default['amount']) ? $default['amount'] : '' ?>">
      </div>
      </div> 
        
      <div class="form-group">  
      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sku"> Tax </label>
          <div class="col-md-4 col-sm-6 col-xs-12">
      <table>
          <tr> 
          <td>
      
<?php $js = "class='form-control' id='ctax' tabindex='-1' style='width:100px; margin-right:5px;' "; 
echo form_dropdown('ctax', $tax, isset($default['tax']) ? $default['tax'] : '', $js); ?>       
          </td>
          <td>
            <input type="number" class="form-control" required readonly id="ttax" name="ttax" value="<?php echo isset($default['taxval']) ? $default['taxval'] : '' ?>">  
          </td>
          </tr>
      </table>   
         </div> 
      </div>  
        
      <div class="form-group">  
      <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sku"> Total </label>
	  <div class="col-md-2 col-sm-6 col-xs-12">
<input type="number" class="form-control" required name="ttotal" id="ttotal" readonly value="<?php echo isset($default['total']) ? $default['total'] : '' ?>">
      </div>
      </div>    
        
                 <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12"> Sales - Acc </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
<input type="text" name="tsalesacc" id="titem" class="form-control" required readonly style="max-width:120px; float:left;" value="<?php echo isset($default['salesacc']) ? $default['salesacc'] : '' ?>"> 
<?php echo anchor_popup(site_url("account/get_list/titem/"), '[ ... ]', $atts1); ?> 
                    </div>
                  </div>
     
    
                  <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12"> Tax - Acc </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
<input type="text" name="ttaxacc" id="titem3" class="form-control" required readonly style="max-width:120px; float:left;" value="<?php echo isset($default['taxacc']) ? $default['taxacc'] : '' ?>"> 
<?php echo anchor_popup(site_url("account/get_list/titem3/"), '[ ... ]', $atts1); ?> 
                    </div>
                  </div>
    
                  <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12"> AR - Acc </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
<input type="text" name="taracc" id="titem4" class="form-control" required readonly style="max-width:120px; float:left;" value="<?php echo isset($default['aracc']) ? $default['aracc'] : '' ?>"> 
<?php echo anchor_popup(site_url("account/get_list/titem4/"), '[ ... ]', $atts1); ?> 
                    </div>
                  </div>
                    
      
      <div class="ln_solid"></div>
      <div class="form-group">
        <div class="col-md-3 col-sm-3 col-xs-12 col-md-offset-3">
          <button type="submit" class="btn btn-primary" id="button"> Save General </button>
        </div>
      </div>
      
	</form>
                      
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
    
    
