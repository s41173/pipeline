
 <!-- Datatables CSS -->
<link href="<?php echo base_url(); ?>js/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/dataTables.tableTools.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>css/icheck/flat/green.css" rel="stylesheet" type="text/css">

<script src="<?php echo base_url(); ?>js/moduljs/tank.js"></script>
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

	var sites_add  = "<?php echo site_url('tank/add_process/');?>";
	var sites_edit = "<?php echo site_url('tank/update_process/');?>";
	var sites_del  = "<?php echo site_url('tank/delete/');?>";
    var sites_update = "<?php echo site_url('tank/update_all/');?>";
	var sites_get  = "<?php echo site_url('tank/update/');?>";
    var sites_details  = "<?php echo site_url('tank/details/');?>";
    var sites_ledger  = "<?php echo site_url('tank/stock_card/');?>";
    var sites_ajax  = "<?php echo site_url('tank/ajax/');?>";
    var sites_primary  = "<?php echo site_url('tank/publish/');?>";
	var sites_attribute  = "<?php echo site_url('tank/attribute/');?>";
	var sites_image  = "<?php echo site_url('tank/image_gallery/');?>";
	var source = "<?php echo $source;?>";
    var sites  = "<?php echo site_url('tank/');?>";
    
    var url  = "<?php echo $graph;?>";
	
    $(document).ready(function (e) {
    
     //chart render
	
	$.getJSON(url, function (result) {
		
		var chart = new CanvasJS.Chart("chartcontainer", {

			theme: "theme1",//theme1
			axisY:{title: "", },
  		    animationEnabled: true, 
			data: [
				{
					type: "column",
					dataPoints: result
				}
			]
		});

		chart.render();
	});
	
	//chart render
        
    // document ready end	
    });
    
</script>

          <div class="row"> 
          
            <div class="col-md-12 col-sm-12 col-xs-12">
                
              <div class="x_panel">
                    
                   <!-- xtitle -->
                      <div class="x_title">
                       <h2> Chart </h2>

                        <ul class="nav navbar-right panel_toolbox">
                          <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a> </li>
                          <li><a class="close-link"><i class="fa fa-close"></i></a> </li>
                        </ul>

                        <div class="clearfix"></div>
                      </div>
                      <!-- xtitle -->

                    <div class="x_content">
                        <div id="chartcontainer" style="height:250px; width:100%;"></div>
                    </div>    
                    
              </div>  
                  
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
                <label> SKU </label> <br>
                <input type="text" name="tsku" id="tsku" class="form-control" style="width:110px; margin-right:5px;" placeholder="SKU">
              </div>   
                             
              <div class="form-group">
                <label> Publish </label> <br>  
                <select name="cpublish" id="cpublish" class="select2_single form-control" style="min-width:120px;">
                   <option value="1"> Publish </option>
                   <option value="0"> Unpublish </option>
                </select>
              </div>
              
<!--              <div class="form-group">
              <input type="text" readonly style="width: 200px" name="reservation" id="d1" class="form-control active" value="">
                 &nbsp; 
              </div>-->
              
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
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal"> <i class="fa fa-plus"></i>&nbsp;Add New </button>
<!--               <a class="btn btn-primary" href="<?php //echo site_url('product/add'); ?>"> <i class="fa fa-plus"></i>&nbsp;Add New </a>-->
<!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal3"> Report  </button>-->
            <a class="btn btn-success" href="<?php echo site_url('tank/ledger'); ?>"> Ledger </a>
               
               <!-- links -->
	           <?php if (!empty($link)){foreach($link as $links){echo $links . '';}} ?>
               <!-- links -->
               </div>
                             
            </div>
          </div>  
    
      <!-- Modal - Add Form -->
      <div class="modal fade" id="myModal" role="dialog">
         <?php $this->load->view('tank_form'); ?>      
      </div>
      <!-- Modal - Add Form -->
      
      <!-- Modal Attribute -->
      <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	     
		 <?php // $this->load->view('product_attribute_frame'); ?> 
      </div>
      <!-- Modal Attribute -->
      
      
      <!-- Modal - Report Form -->
      <div class="modal fade" id="myModal3" role="dialog">
         <?php // $this->load->view('product_report_panel'); ?>    
      </div>
      <!-- Modal - Report Form -->
              
      <!-- Modal - Detail -->
      <div class="modal fade" id="myModal9" role="dialog">
        <?php $this->load->view('tank_details'); ?>    
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
