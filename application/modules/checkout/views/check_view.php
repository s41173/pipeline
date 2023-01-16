
 <!-- Datatables CSS -->
<link href="<?php echo base_url(); ?>js/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/dataTables.tableTools.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>css/icheck/flat/green.css" rel="stylesheet" type="text/css">

<script src="<?php echo base_url(); ?>js/moduljs/checkout.js"></script>
<script src="<?php echo base_url(); ?>js-old/register.js"></script>

<!--canvas js-->
<script type="text/javascript" src="<?php echo base_url().'js-old/' ?>canvasjs.min.js"></script>

<!-- Date time picker -->
 <script type="text/javascript" src="http://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
 
 <!-- Include Date Range Picker -->
<script type="text/javascript" src="http://cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="http://cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />


<script type="text/javascript">

	var sites_add  = "<?php echo site_url('checkout/add_process/');?>";
	var sites_edit = "<?php echo site_url('checkout/update_process/');?>";
	var sites_del  = "<?php echo site_url('checkout/delete/');?>";
	var sites_get  = "<?php echo site_url('checkout/update/');?>";
    var sites_ledger  = "<?php echo site_url('checkout/get/');?>";
    var sites_primary  = "<?php echo site_url('checkout/publish/');?>";
    var sites_ajax  = "<?php echo site_url('checkout/');?>";
	var source = "<?php echo $source;?>";
	
</script>

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

          <div class="row"> 
          
            <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="x_panel" >
                  
    <div class="title"> <?php $flashmessage = $this->session->flashdata('message'); ?> </div>
    <p class="message"> <?php echo ! empty($message) ? $message : '' . ! empty($flashmessage) ? $flashmessage : ''; ?> </p>  
                  
                <div class="x_content">  
           
           <!-- searching form -->
           
           <form id="xsearchform" class="form-inline" method="post" action="<?php echo $form_action; ?>">
              
              <div class="form-group"> 
              <label> Check No </label> <br>      
    <input type="text" name="tno" id="tno" class="form-control" style="width:120px;" placeholder="Check No"> 
              </div>
               
            <div class="form-group">
                <label> Period </label> <br>
                <div class="col-md-9 col-sm-9 col-xs-12">     
        <input type="text" readonly style="width: 200px" name="reservation" id="d1" class="form-control active" value=""> 
                </div>
            </div>
               
            <div class="form-group">
                <label> Type </label> <br>
                <div class="col-md-9 col-sm-9 col-xs-12">     
                    
             <select name="ctype" class="form-control">
                <option value="purchase" selected="selected"> Purchasing </option>
				<option value="ar"> Sales </option>
             </select>
                </div>
            </div>
              
              <div class="btn-group"> <br>
               <button type="submit" class="btn btn-primary button_inline"> Filter </button>
               <button type="reset" onClick="" class="btn btn-danger button_inline"> Clear </button>
              </div>
          </form> <br>
           
           <!-- searching form -->
           
              
<form class="form-inline" id="cekallform" method="post" action="<?php echo ! empty($form_action_del) ? $form_action_del : ''; ?>">
  <!-- table -->

  <?php echo ! empty($table) ? $table : ''; ?>            
  <!-- Check All Function -->  
</form>       
                

<div class="btn-group">

<button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal3"> Report </button>
<!-- links -->
<?php if (!empty($link)){foreach($link as $links){echo $links . '';}} ?>
<!-- links -->

</div>                                   
                    
            </div>
                                               
            </div>
          </div>  
    
      <!-- Modal - Add Form -->
      <div class="modal fade" id="myModal" role="dialog">  
      </div>
      <!-- Modal - Add Form -->
              
       <!-- Modal - Add Form -->
      <div class="modal fade" id="myModal2" role="dialog"> 
      </div>
      <!-- Modal - Add Form -->
      
      
      <!-- Modal - Report Form -->
      <div class="modal fade" id="myModal3" role="dialog">
         <?php $this->load->view('check_report_panel'); ?>    
      </div>
      <!-- Modal - Report Form -->
      
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
