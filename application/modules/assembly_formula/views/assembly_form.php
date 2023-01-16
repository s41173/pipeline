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

<script src="<?php echo base_url(); ?>js/moduljs/assembly_formula.js"></script>
<script src="<?php echo base_url(); ?>js-old/register.js"></script>

<script type="text/javascript">

	var sites_add  = "<?php echo site_url('assembly_formula/add_process/');?>";
	var sites_edit = "<?php echo site_url('assembly_formula/update_process/');?>";
	var sites_del  = "<?php echo site_url('assembly_formula/delete/');?>";
	var sites_get  = "<?php echo site_url('assembly_formula/update/');?>";
    var sites  = "<?php echo site_url('assembly_formula/');?>";
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
    
$atts2 = array(
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
                    
$atts3 = array(
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
  
  <div id="step-1">
    <!-- form -->
    <form id="journalformdata" data-parsley-validate class="form-horizontal form-label-left" method="POST" 
    action="<?php echo $form_action; ?>" >
		
    <style type="text/css">
       .xborder{ border: 1px solid red;}
       #custtitlebox{ height: 90px; background-color: #E0F7FF; border-top: 3px solid #2A3F54; margin-bottom: 10px; }
        #amt{ color: #000; margin-top: 35px; text-align: right; font-weight: bold;}
        #amt span{ color: blue;}
        .labelx{ font-weight: bold; color: #000;}
        #table_summary{ font-size: 12px; color: #000;}
        .amt{ text-align: right;}
    </style>
        
<?php
    
$atts1 = array(
	  'class'      => 'btn btn-primary button_inline',
	  'title'      => 'COA - List',
	  'width'      => '800',
	  'height'     => '600',
	  'scrollbars' => 'yes',
	  'status'     => 'yes',
	  'resizable'  => 'yes',
	  'screenx'    =>  '\'+((parseInt(screen.width) - 800)/2)+\'',
	  'screeny'    =>  '\'+((parseInt(screen.height) - 600)/2)+\'',
);

?>

<!-- form atas   -->
    <div class="row">
       
<!-- div untuk customer place  -->
       <div id="custtitlebox" class="col-md-12 col-sm-12 col-xs-12">
            
           <div class="form-group">
               
            <div class="col-md-4 col-sm-12 col-xs-12">
                   <label class="control-label labelx"> Product - Qty </label> <br>
<table>
<tr>
    <td>
<input id="titems" name="tproduct" class="form-control col-md-3 col-xs-12" type="text" style="width:150px;" value="<?php echo isset($default['product']) ? $default['product'] : '' ?>">
<?php echo anchor_popup(site_url("product/get_list/titems/".$branchid.'/1'), '[ ... ]', $atts1); ?>
    </td>
    <td>
<input type="text" class="form-control" name="tqty" style="width:60px; margin-left:10px;" value="<?php echo isset($default['qty']) ? $default['qty'] : '1' ?>">
    </td>
</tr>                  
</table>
            </div>
                         
               <div class="col-md-2 col-sm-12 col-xs-12">
                   <label class="control-label labelx"> Currency </label> <br>
<?php  $js = "class='form-control' id='ccurrency' tabindex='-1' style='width:150px; float:left; margin-right:10px;' ";
echo form_dropdown('ccurrency', $currency, isset($default['currency']) ? $default['currency'] : '', $js); ?>
<input type="hidden" name="tid" value="<?php echo $pid; ?>">
               </div>
               
               <div class="col-md-2 col-sm-12 col-xs-12">
                   <label class="control-label labelx"> Transaction Date </label>
           <input type="text" title="Date" class="form-control" id="ds1" name="tdate" required
           value="<?php echo isset($default['dates']) ? $default['dates'] : '' ?>" /> 
               </div>
               
                <div class="col-md-3 col-sm-12 col-xs-12">
                   <label class="control-label labelx"> Account </label>
<?php $js = "class='form-control' id='cacc' tabindex='-1' style='min-width:150px;' "; 
echo form_dropdown('cacc', $account, isset($default['acc']) ? $default['acc'] : '', $js); ?>
               </div>
               
           </div>
           
       </div>
<!-- div untuk account place  -->    
        
 <div class="col-md-3 col-sm-12 col-xs-12">
     
<table>
<tr>
    <td> <label class="control-label labelx"> Note </label> <br>
<textarea name="tnote" style="width:100%;" rows="3"><?php echo isset($default['note']) ? $default['note'] : '' ?></textarea>
    </td>
</tr>        
    
<tr>
  <td> <label class="control-label labelx"> Stock-Acc </label> <br>
<input id="titemstockacc" class="form-control col-md-3 col-xs-12" type="text" name="titemstockacc" style="width:100px;" required value="<?php echo isset($default['stockacc']) ? $default['stockacc'] : '' ?>">
<?php echo anchor_popup(site_url("account/get_list/titemstockacc"), '[ ... ]', $atts2); ?>
&nbsp;
  </td>    
</tr>
    
<tr>
  <td> <label class="control-label labelx"> Cost-Acc </label> <br>
<input id="titemcostacc" class="form-control col-md-3 col-xs-12" type="text" name="titemcostacc" style="width:100px;" required value="<?php echo isset($default['costacc']) ? $default['costacc'] : '' ?>">
<?php echo anchor_popup(site_url("account/get_list/titemcostacc"), '[ ... ]', $atts2); ?>
&nbsp;
  </td>    
</tr>
    
</table>     
        
 </div>


<!-- div staff -->
    <div class="col-md-3 col-sm-12 col-xs-12"> 
<table>
<tr>
    <td> <label class="control-label labelx"> Project </label> <br>
<input type="text" name="tproject" class="form-control" placeholder="Project" value="<?php echo isset($default['project']) ? $default['project'] : '' ?>">
    </td>
</tr>        
    
<tr>
  <td> <label class="control-label labelx"> Docno </label> <br>
<input type="text" name="tdocno" class="form-control" placeholder="Docno" value="<?php echo isset($default['docno']) ? $default['docno'] : '' ?>"> 
  </td>    
</tr>

<tr>
    <td> <label class="control-label labelx"> Storage / Outlet </label> <br>
<?php  $js = "class='form-control' id='coutlet' tabindex='-1' style='width:150px; float:left; margin-right:10px;' ";
echo form_dropdown('coutlet', $branch, isset($default['outlet']) ? $default['outlet'] : '', $js); ?>
    </td>
</tr>    
    
</table>
         
    </div>
<!-- div staff -->
        
<!-- div staff -->
    <div class="col-md-3 col-sm-12 col-xs-12">
       
<table>
<tr>
    <td> <label class="control-label labelx"> Costs </label> <br>
<input type="text" name="tcost" class="form-control" readonly value="<?php echo isset($default['cost']) ? $default['cost'] : '' ?>">
    </td>
</tr>        
    
<tr>
  <td> <label class="control-label labelx"> Total </label> <br>
<input type="text" name="ttotal" id="ttotal" class="form-control" readonly value="<?php echo isset($default['total']) ? $default['total'] : '' ?>"> 
  </td>    
</tr>
    
<tr>
  <td> <label class="control-label labelx"> Prod-Cost(%) </label> <br>
<input type="number" maxlength="2" name="tprodcost" id="tprodcost" class="form-control" style="width:80px;" value="<?php echo isset($default['prodcost']) ? $default['prodcost'] : '' ?>"> 
  </td>    
</tr>
    
</table>
        
    </div>
<!-- div staff -->
        
    <div class="col-md-2 col-sm-12 col-xs-12">
        
<table>
   <tr>
<td>
<label class="control-label labelx"> Tax </label> <br>
<?php  $js = "class='form-control' id='ctax' tabindex='-1' style='width:120px; float:left;' ";
echo form_dropdown('ctax', $tax, isset($default['taxid']) ? $default['taxid'] : '', $js); ?>
</td>
   </tr>
    <tr>
<td>
<label class="control-label labelx"> Tax Amount </label> <br>
<input type="text" name="ttax" class="form-control" readonly value="<?php echo isset($default['tax']) ? $default['tax'] : '' ?>"> 
</td>
   </tr>
</table>
        
       
     </div>

</div>
<!-- form atas   -->
      
      <div class="ln_solid"></div>
      <div class="form-group">
        <div class="col-md-4 col-sm-3 col-xs-12 col-md-offset-9">
          <div class="btn-group">    
          <button type="submit" class="btn btn-success" id="button"> Save </button>
          <button type="reset" class="btn btn-danger" id=""> Cancel </button>
          <a class="btn btn-primary" href="<?php echo site_url('cashin/add/'); ?>"> New Transaction </a>
          </div>
        </div>
      </div>
      
	</form>
      
    <!-- end div layer 1 -->
      
<!-- form transaction table  -->
      
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
    
 <!-- searching form -->
           
   <form id="ajaxtransform" class="form-inline" method="post" action="<?php echo $form_action_item; ?>">
      
       <div class="form-group">
        <label class="control-label labelx"> Product </label> <br>
           <input id="titems2" class="form-control col-md-3 col-xs-12" type="text" readonly name="tproduct" required style="width:150px;">
          <?php echo anchor_popup(site_url("product/get_list/titems2"), '[ ... ]', $atts1); ?>
<input type="hidden" name="tbranchid" value="<?php echo isset($default['branch']) ? $default['branch'] : '' ?>">
          &nbsp;
      </div>
              
      <div class="form-group">
        <label class="control-label labelx"> Qty </label> <br>
        <input type="number" name="tqty" id="tqty" class="form-control" style="width:90px;" maxlength="5" required value="0"> &nbsp;
      </div>         

      <div class="form-group"> <br>
       <div class="btn-group">     
       <button type="submit" class="btn btn-primary button_inline"> Post </button>
       <button type="reset" onClick="" class="btn btn-danger button_inline"> Reset </button>
       </div>
      </div>
       
  </form> <br>


   <!-- searching form --> 
        
    </div>
    
<!-- table -->
  <div class="col-md-12 col-sm-12 col-xs-12">  
    <div class="table-responsive">
      <table class="table table-striped jambo_table bulk_action">
        <thead>
          <tr class="headings">
            <th class="column-title"> No </th>
            <th class="column-title"> Product </th>
            <th class="column-title"> Qty </th>
            <th class="column-title"> Unit Price </th>
            <th class="column-title"> Amount </th>
            <th class="column-title no-link last"><span class="nobr">Action</span>
            </th>
            <th class="bulk-actions" colspan="7">
              <a class="antoo" style="color:#fff; font-weight:500;">Bulk Actions ( <span class="action-cnt"> </span> ) <i class="fa fa-chevron-down"></i></a>
            </th>
          </tr>
        </thead>

        <tbody>
            
        <?php
            
            function product($val)
            {
                $acc = new Product_lib();
                return $acc->get_name($val);
            }
            
            function unit($val)
            {
                $acc = new Product_lib();
                return $acc->get_unit($val);
            }
            
            if ($items)
            {
                $i=1;
                foreach($items as $res)
                {
                    echo "
                     <tr class=\"even pointer\">
                        <td> ".$i." </td>
                        <td>".product($res->product_id)." </td>
                        <td>".$res->qty.''.unit($res->product_id)." </td>
                   <td class=\"a-right a-right \"> ".idr_format(floatval($res->price/$res->qty))." </td>
                   <td class=\"a-right a-right \"> ".idr_format($res->price)." </td>
<td class=\" last\"> 
<a class=\"btn btn-danger btn-xs text-remove\" id=\"".$res->id."\"> <i class=\"fa fas-2x fa-trash\"> </i> </a> 
</td>
                      </tr>
                    "; $i++;
                }
            }
            
        ?> 

        </tbody>
      </table>
    </div>
    </div>
<!-- table -->
    
</div>
<!-- form transaction table  -->  
      
<!-- form transaction table2  -->
      
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
    
 <!-- searching form -->
           
   <form id="ajaxtransform2" class="form-inline" method="post" action="<?php echo $form_action_cost; ?>">
      
      <div class="form-group">
        <label class="control-label labelx"> Notes </label> <br>
           <textarea class="form-control" name="tnotes" cols="30">Prod-Cost</textarea> &nbsp;
      </div>
                     
      <div class="form-group">
        <label class="control-label labelx"> Amount </label> <br>
        <input type="number" name="tamount" id="tcostamount" class="form-control" style="width:130px;" maxlength="8" required> &nbsp;
      </div>          

      <div class="form-group"> <br>
       <div class="btn-group">     
       <button type="submit" class="btn btn-primary button_inline"> Post </button>
       <button type="reset" onClick="" class="btn btn-danger button_inline"> Reset </button>
       </div>
      </div>
       
  </form> <br>
   <!-- searching form --> 
        
    </div>
    
<!-- table -->
  <div class="col-md-12 col-sm-12 col-xs-12">  
    <div class="table-responsive">
      <table class="table table-striped jambo_table bulk_action">
        <thead>
          <tr class="headings">
            <th class="column-title"> No </th>
            <th class="column-title"> Notes </th>
            <th class="column-title"> Amount </th>
            <th class="column-title no-link last"><span class="nobr">Action</span>
            </th>
            <th class="bulk-actions" colspan="7">
              <a class="antoo" style="color:#fff; font-weight:500;">Bulk Actions ( <span class="action-cnt"> </span> ) <i class="fa fa-chevron-down"></i></a>
            </th>
          </tr>
        </thead>

        <tbody>
            
        <?php
            
            if ($itemcost)
            {
                
                $i=1;
                foreach($itemcost as $res)
                {
                    echo "
                     <tr class=\"even pointer\">
                        <td> ".$i." </td>
                        <td>".$res->notes." </td>
                        <td class=\"a-right a-right \"> ".idr_format($res->amount)." </td>
<td class=\" last\"> 
<a class=\"btn btn-danger btn-xs text-remove-cost\" id=\"".$res->id."\"> <i class=\"fa fas-2x fa-trash\"> </i> </a> 
</td>
                      </tr>
                    "; $i++;
                }
            }
            
        ?> 

        </tbody>
      </table>
    </div>
    </div>
<!-- table -->
    
</div>
<!-- form transaction table  -->  
        
  </div>
                  
     </div>
       
       <div class="btn-group">
         <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#myModal4"> CSV-Import  </button>
         <!-- links -->
         <?php if (!empty($link)){foreach($link as $links){echo $links . '';}} ?>
         <!-- links -->
       </div>
                  
      <!-- Modal - Import Form -->
      <div class="modal fade" id="myModal4" role="dialog">
        <?php //$this->load->view('stock_adjustment_import'); ?>    
      </div>
      <!-- Modal - Import Form -->
                     
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
    
    
