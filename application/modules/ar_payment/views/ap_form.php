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

<script src="<?php echo base_url(); ?>js/moduljs/ar_payment.js"></script>
<script src="<?php echo base_url(); ?>js-old/register.js"></script>

<script type="text/javascript">

	var sites_add  = "<?php echo site_url('ar_payment/add_process/');?>";
	var sites_edit = "<?php echo site_url('ar_payment/update_process/');?>";
	var sites_del  = "<?php echo site_url('ar_payment/delete/');?>";
	var sites_get  = "<?php echo site_url('ar_payment/update/');?>";
    var sites  = "<?php echo site_url('ar_payment/');?>";
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
	  'title'      => 'Product - List',
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

<!-- form atas   -->
    <div class="row">
       
<!-- div untuk customer place  -->
       <div id="custtitlebox" class="col-md-12 col-sm-12 col-xs-12">
            
           <div class="form-group">
               
               <div class="col-md-1 col-sm-12 col-xs-12">
                   <label class="control-label labelx"> No </label> <br>
     <input type="number" name="tno" id="tno" class="form-control" style="width:80px;" value="<?php echo $code; ?>">   
     <input type="hidden" name="tid" value="<?php echo $pid; ?>">
               </div>
               
               <div class="col-md-2 col-sm-12 col-xs-12">
                   <label class="control-label labelx"> Currency </label>
         <?php  $js = "class='form-control' id='ccurrency' tabindex='-1' style='min-width:120px; float:left; margin-right:10px;' ";
	     echo form_dropdown('ccurrency', $currency, isset($default['currency']) ? $default['currency'] : '', $js); ?>
               </div>
               
               <div class="col-md-2 col-sm-12 col-xs-12">
                   <label class="control-label labelx"> Transaction Date </label>
           <input type="text" title="Date" class="form-control" id="ds1" name="tdate" required
           value="<?php echo isset($default['date']) ? $default['date'] : '' ?>" /> 
               </div>
               
               <div class="col-md-4 col-sm-12 col-xs-12">
                   <label class="control-label labelx"> Customer </label> <br>
<?php $js = "class='select2_single form-control' id='cvendor' tabindex='-1' style='min-width:150px;' "; 
echo form_dropdown('cvendor', $customer, isset($venid) ? $venid : '', $js); ?>
               </div>
               
               
               <div class="col-md-3 col-sm-12 col-xs-12">
                   <h2 id="amt"> Total : <span class="amt"> <?php echo isset($default['balancecek']) ? idr_format($default['balancecek']) : '0'; ?> </span>,- </h2>
               </div>
               
           </div>
           
       </div>
<!-- div untuk account place  -->
        
 <div class="col-md-2 col-sm-12 col-xs-12">
    <label class="control-label labelx"> Account </label>
    <select name="cacc" class="form-control">
        <option value="bank"<?php echo set_select('cacc', 'bank', isset($default['acc']) && $default['acc'] == 'bank' ? TRUE : FALSE); ?>> Bank </option>
        <option value="cash"<?php echo set_select('cacc', 'cash', isset($default['acc']) && $default['acc'] == 'cash' ? TRUE : FALSE); ?>> Cash </option>
        <option value="pettycash"<?php echo set_select('cacc', 'pettycash', isset($default['acc']) && $default['acc'] == 'pettycash' ? TRUE : FALSE); ?>> Petty Cash </option>
    </select>
    
    <label class="control-label labelx"> Doc No </label>
    <input type="text" class="form-control" name="tdocno" id="tdocno" value="<?php echo isset($default['docno']) ? $default['docno'] : '' ?>">
     
    <label class="control-label labelx"> Rate </label>
    <input type="text" class="form-control" name="trate" id="trate" value="<?php echo isset($default['rate']) ? $default['rate'] : '' ?>"> 
     
</div>

<!-- Payment Details -->
       <div class="col-md-2 col-sm-12 col-xs-12 col-md-offset-1">
        
       <fieldset> <legend style="margin:0; padding:0;"> Payment Details </legend>
           
           <label class="control-label labelx"> Late Charges </label>
<input type="number" class="form-control" id="tlate" name="tlate" value="<?php echo isset($default['late']) ? $default['late'] : '' ?>" >
          
          <label class="control-label labelx"> Total Discount </label>
<input type="number" class="form-control" readonly id="ttttdiscountax" name="ttdiscount" value="<?php echo isset($default['tdiscount']) ? $default['tdiscount'] : '' ?>">
           
          <label class="control-label labelx"> Balance </label>
<input type="number" class="form-control" readonly id="tbalance" name="tbalance" value="<?php echo isset($default['balance']) ? $default['balance'] : '' ?>"> 
           
       </fieldset>       
                    
       </div>
<!-- Payment Details -->

<!-- cek details -->
    <div class="col-md-6 col-sm-12 col-xs-12 col-md-offset-1">
        
      <fieldset> <legend style="margin:0; padding:0;"> Cheque Details </legend>
          
          <style type="text/css">
              #ctable td{ padding: 3px; }
          </style>
          <table id="ctable">
			
            <tr>
				<td> <label class="control-label labelx"> Post - Dated </label> </td>  <td> &nbsp; </td>
				<td> <?php $js = "class='flat'"; echo form_checkbox('cpost', 1, set_value('cpost', isset($default['status']) ? $default['status'] : 'FALSE'), $js); ?> &nbsp; </td>
			</tr>
            
			<tr>
				<td> <label class="control-label labelx" for="tcheck"> Check - No </label> </td>  <td> &nbsp; </td>
                <td>
                <input type="text" name="tcheck" class="form-control" title="Check No" style="width:120px;"
				value="<?php echo set_value('tcheck', isset($default['check']) ? $default['check'] : ''); ?>" placeholder="Check No" /> </td>
			</tr>
            
            <tr>
            <td> <label class="control-label labelx" for="tcheck"> Check - Acc </label> </td>  <td> </td>
              
            <td>
<input type="text" name="tcheckaccno" id="tcheckaccno" class="form-control" style="width:120px;" value="<?php echo set_value('tcheckaccno', isset($default['checkaccno']) ? $default['checkaccno'] : ''); ?>" placeholder="Account No"> 
            </td>    
                
            <td>
<input type="text" class="form-control" name="tcheckaccname" id="tcheckaccname" value="<?php echo set_value('tcheckaccname', isset($default['checkaccname']) ? $default['checkaccname'] : ''); ?>" placeholder="Account Name">
            </td>    
                
            </tr>
              
            <tr>	
			<td> </td> <td> </td>
			<td colspan="2"> 
            <input type="text" class="form-control" name="tccbank" id="taccbank" placeholder="Acc Bank" 
            value="<?php echo set_value('tccbank', isset($default['checkaccbank']) ? $default['checkaccbank'] : ''); ?>"> </td>
			</tr>
			
			<tr>	
			<td> <label class="control-label labelx" for="cbank"> Account </label> </td> <td> </td>
			<td colspan="2"> <?php $js = 'class="form-control"'; echo form_dropdown('cbank', $bank, isset($default['bank']) ? $default['bank'] : '', $js); ?> </td>
			</tr>
			
			<tr>
				<td> <label class="control-label labelx" for="tbalancecek"> Balance </label></td> <td> </td> 
				<td> <input type="number" id="tbalancecek" name="tbalancecek" title="Balance" class="form-control" style="width:120px;"
			    value="<?php echo set_value('tbalancecek', isset($default['balancecek']) ? $default['balancecek'] : '0'); ?>" />  </td> 
			</tr>
			
			<tr>	
				 <td> <label class="control-label labelx" for="tdue"> Due Date </label> </td> <td> </td>
				 <td>  
				   <input type="Text" name="tdue" id="ds2" title="Due date" class="form-control" style="width:120px;"
				   value="<?php echo set_value('tdue', isset($default['due']) ? $default['due'] : ''); ?>" /> 
				</td>
			</tr>
			
		</table>
          
      </fieldset>
    </div>
<!-- cek details -->
        

</div>
<!-- form atas   -->
      
      <div class="ln_solid"></div>
      <div class="form-group">
        <div class="col-md-4 col-sm-3 col-xs-12 col-md-offset-9">
          <div class="btn-group">    
          <button type="submit" class="btn btn-success" id="button"> Save </button>
          <button type="reset" class="btn btn-danger" id=""> Cancel </button>
          <a class="btn btn-primary" href="<?php echo site_url('ap_payment/add/'); ?>"> New Transaction </a>
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
        <label class="control-label labelx"> Transaction </label> <br>
           <input id="titem" class="form-control col-md-2 col-xs-12" type="text" readonly name="titem" required style="width:100px;" placeholder="SO">
          <div class="btn-group">    
          <?php echo anchor_popup(site_url("sales/get_list/".$default['currency'].'/'.$venid), '[ ... ]', $atts1); ?>
          <button type="button" id="bget" class="btn btn-success button_inline"> Get </button>
          </div>
          &nbsp;
      </div>
      
      <div class="form-group">
        <label class="control-label labelx"> Balance </label> <br>
        <input type="number" name="tnominal" id="tnominal" class="form-control" style="width:100px;" maxlength="5" required value=""> &nbsp;
      </div>   
       
      <div class="form-group">
        <label class="control-label labelx"> Discount </label> <br>
        <input type="number" name="tdiscount" id="tdiscount" class="form-control" style="width:100px;" maxlength="5" required value=""> &nbsp;
      </div>   
       
      <div class="form-group">
        <label class="control-label labelx"> Amount </label> <br>
        <input type="number" name="tamount" id="tamounts" class="form-control" style="width:120px;" maxlength="10" required value=""> &nbsp;
      </div>

      <div class="form-group"> <br>
       <div class="btn-group">    
       <button type="submit" class="btn btn-primary button_inline"> Post </button>
       <button type="button" onClick="load_data();" class="btn btn-danger button_inline"> Reset </button>
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
            <th class="column-title"> Code </th>
            <th class="column-title"> Discount </th>
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
            
            function product($val,$type='name')
            {
                $acc = new Product_lib();
                if ($type == 'name'){ return $acc->get_name($val); }
                elseif ($type == 'unit'){return $acc->get_unit($val); }
                elseif ($type == 'sku'){ return $acc->get_sku($val); }
            }
            
            if ($items)
            {
                $i=1;
                foreach($items as $res)
                {
                    echo "
                     <tr class=\"even pointer\">
                        <td> ".$i." </td>
                        <td>".$res->code."-0".$res->no."</td>
                        <td class=\"a-right a-right \"> ".idr_format($res->discount)." </td>
                        <td class=\"a-right a-right \"> ".idr_format($res->amount)." </td>
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
    
    
