
 <!-- Datatables CSS -->
<link href="<?php echo base_url(); ?>js/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/dataTables.tableTools.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>css/icheck/flat/green.css" rel="stylesheet" type="text/css">

<script src="<?php echo base_url(); ?>js/moduljs/sounding.js"></script>
<!--<script src="<?php//echo base_url(); ?>js-old/register.js"></script>-->

<!--canvas js-->
<script type="text/javascript" src="<?php echo base_url().'js-old/' ?>canvasjs.min.js"></script>

<!-- Date time picker -->
 <script type="text/javascript" src="http://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
 
 <!-- Include Date Range Picker -->
<script type="text/javascript" src="http://cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="http://cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />

<style type="text/css">
    
    .normal_p{ text-decoration: line-through; margin: 0;}
    .discount_p { color: red;}
</style>

<!-- bootstrap toogle -->
<!--<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>-->

<script type="text/javascript">

	var sites_add  = "<?php echo site_url('sounding/add_process/');?>";
	var sites_edit = "<?php echo site_url('sounding/update_process/');?>";
	var sites_del  = "<?php echo site_url('sounding/delete/');?>";
    var sites_update = "<?php echo site_url('sounding/update_all/');?>";
	var sites_get  = "<?php echo site_url('sounding/update/');?>";
    var sites_details  = "<?php echo site_url('sounding/invoice/');?>";
    var sites_primary  = "<?php echo site_url('sounding/confirmation/');?>";
	var source = "<?php echo $source;?>";
    var sites  = "<?php echo site_url('sounding');?>";
        
</script>

          <div class="row"> 
          
            <div class="col-md-12 col-sm-12 col-xs-12">
                  
              <!--  batas xtitle 2  -->    
                
              <div class="x_panel" >
                   
              <!-- xtitle -->
              <div class="x_title">
                
               <h2> Filter </h2>
                
                <ul class="nav navbar-right panel_toolbox">
                  <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                  <li><a class="close-link"><i class="fa fa-close"></i></a> </li>
                </ul>
                
                <div class="clearfix"></div>
              </div>
              <!-- xtitle -->
                
                <div class="x_content">
           
           <!-- searching form -->
           
           <form id="searchform" class="form-inline">
              
               <div class="form-group">
                <label> Doc-No </label> <br>
         <?php $js = "class='form-control select2_single' id='cregist_search' tabindex='-1' style='width:240px;' "; 
         echo form_dropdown('cregister', $docno, isset($default['']) ? $default[''] : '', $js); ?>
              </div>  
               
<!--
                <div class="form-group">
                <label> Origin </label> <br>
    <?php $js = "class='form-control select2_single' id='ccontract_search' tabindex='-1' style='width:300px;' "; 
   // echo form_dropdown('ccontract', $contract, isset($default['']) ? $default[''] : '', $js); ?>
              </div>  
-->
                                      
              <div class="btn-group"> <br>
               <button type="submit" class="btn btn-primary button_inline"> Filter </button>
               <button type="reset" onClick="" class="btn btn-success button_inline"> Clear </button>
               <button type="button" onClick="load_data();" class="btn btn-danger button_inline"> Reset </button>
              </div>
          </form> <br>

           
           <!-- searching form -->
           
              
<form class="form-inline" id="cekallform" method="post" action="<?php //echo ! empty($form_action_del) ? $form_action_del : ''; ?>">

                  <!-- table -->
                  
                  <div class="table-responsive">
                    <?php echo ! empty($table) ? $table : ''; ?>            
                  </div>
                  
                  <div class="form-group" id="chkbox">
                    Check All : 
        <button type="submit" id="cekallbutton" class="btn btn-danger btn-xs" name="delete">
           <span class="glyphicon glyphicon-trash"></span>
        </button>
                                
        <button type="button" onclick="load_deleted()" class="btn btn-warning btn-xs" name="delete" title="Deleted List">
           <span class="glyphicon glyphicon-trash"></span>
        </button>
                      
        <button type="button" id="cekallupdate" class="btn btn-primary btn-xs" name="update">
           <span class="glyphicon glyphicon-book"></span>
        </button>
                      
                  </div>
                  <!-- Check All Function -->
                  
          </form>       
             </div>
               
               <div class="btn-group">  
               <!-- Trigger the modal with a button --> 
<button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal3"> Report  </button>
               
               <!-- links -->
	           <?php if (!empty($link)){foreach($link as $links){echo $links . '';}} ?>
               <!-- links -->
               </div>
                             
            </div>
          </div>  
    
      <!-- Modal - Add Form -->
      <div class="modal fade" id="myModal" role="dialog">
         <?php //$this->load->view('registration_form'); ?>      
      </div>
      <!-- Modal - Add Form -->
      
      <!-- Modal Attribute -->
      <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	     
		 <?php // $this->load->view('product_attribute_frame'); ?> 
      </div>
      <!-- Modal Attribute -->
      
      
      <!-- Modal - Report Form -->
      <div class="modal fade" id="myModal3" role="dialog">
         <?php $this->load->view('sounding_report_panel'); ?>    
      </div>
      <!-- Modal - Report Form -->
              
      <!-- Modal - Detail -->
      <div class="modal fade" id="myModal9" role="dialog">
        <?php // $this->load->view('tank_details'); ?>    
      </div>
      <!-- Modal - Detail -->
      
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
