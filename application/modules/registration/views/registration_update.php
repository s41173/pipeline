 <!-- Datatables CSS -->
<link href="<?php echo base_url(); ?>js/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>js/datatables/dataTables.tableTools.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>css/icheck/flat/green.css" rel="stylesheet" type="text/css">

<!-- Date time picker -->
 <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
 
 <!-- Include Date Range Picker -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />


<style type="text/css">
  a:hover { text-decoration:none;}
</style>

<script src="<?php echo base_url(); ?>js/moduljs/registration.js"></script>
<script src="<?php echo base_url(); ?>js-old/register.js"></script>

<script type="text/javascript">

	var sites_add  = "<?php echo site_url('registration/add_process/');?>";
	var sites_edit = "<?php echo site_url('registration/update_process/');?>";
	var sites_del  = "<?php echo site_url('registration/delete/');?>";
	var sites_get  = "<?php echo site_url('registration/update/');?>";
    var sites  = "<?php echo site_url('registration/');?>";
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
	  'width'      => '1024',
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
    <form id="ajaxtransform" data-parsley-validate class="form-horizontal form-label-left" method="POST" 
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
                   <label class="control-label labelx"> Code </label> <br>
     <input type="text" name="tno" id="tno" readonly class="form-control" style="width:85px;" value="<?php echo $default['code']; ?>">           
               </div>
               
               <div class="col-md-2 col-sm-12 col-xs-12">
                   <label class="control-label labelx"> Type </label>
                   <input type="text" name="ttype" id="ttype" class="form-control" readonly style="width:120px;" value="<?php echo $default['type']; ?>"> 
               </div>
               
                <div class="col-md-3 col-sm-12 col-xs-12">
                   <label class="control-label labelx"> Doc-No </label> <br>
<input type="text" name="tdocno" id="tdocno" class="form-control" readonly style="width:200px;" value="<?php echo $default['docno']; ?>"> 
               </div>
               
               <div class="col-md-2 col-sm-12 col-xs-12">
                   <label class="control-label labelx"> Tank Farm </label>
           <input type="text" title="Tank Farm" class="form-control" name="ttank" readonly
           value="<?php echo isset($default['tank']) ? $default['tank'] : '' ?>" style="width:200px;" /> 
               </div>
               
              
               <div class="col-md-3 col-sm-12 col-xs-12">
                   <h2 id="amt" style="text-align:justify;"> Origin Qty &nbsp; &nbsp; &nbsp; : <span class="amt"> <?php echo $qty_terima; ?> </span> <br/>
                   
                   Received Qty : <span class="amt"> <?php echo $qty_terima; ?> </span> </h2>
               </div>
               
           </div>
           
       </div>
<!-- div untuk account place  -->
        
 <div class="col-md-2 col-sm-12 col-xs-12">
    <label class="control-label labelx"> Transaction Date </label>
    <input type="text" title="Date" class="form-control" id="ds5" name="tdate" required
           value="<?php echo isset($default['date']) ? $default['date'] : '' ?>" /> 
    
    <label class="control-label labelx"> Start Transfer </label>
    <input type="text" class="form-control" name="tstart" id="ds6" value="<?php echo isset($default['start']) ? $default['start'] : '' ?>"> 
     
    <label class="control-label labelx"> End Transfer </label>
    <input type="text" class="form-control" name="tend" id="ds7" value="<?php echo isset($default['end']) ? $default['end'] : '' ?>"> 
     
</div>

<!-- payment method -->
    <div class="col-md-4 col-sm-12 col-xs-12">
          <label class="control-label labelx"> PIC - IBL </label>
<table>
    <tr> 
        <td> 
<?php $js = "class='form-control select2_single' id='cpicibl' tabindex='-1' style='width:255px; margin-bottom:10px;' "; 
echo form_dropdown('cpicibl', $pic_ibl, isset($default['pic_ibl']) ? $default['pic_ibl'] : '', $js); ?>
        </td>
    </tr>
    <tr>
        <td> <br/>
<input type="text" class="form-control" id="tpicibl" name="tpicibl" style="width:255px;" >   
        </td>
    </tr>
</table>
        
    
          <label class="control-label labelx"> PIC - OBL </label>
<table>
    <tr> 
        <td> 
<?php $js = "class='form-control select2_single' id='cpicobl' tabindex='-1' style='width:255px;' "; 
echo form_dropdown('cpicobl', $pic_obl, isset($default['pic_obl']) ? $default['pic_obl'] : '', $js); ?>
        </td>
    </tr>
    <tr>
        <td> <br/>
<input type="text" class="form-control" id="tpicobl" name="tpicobl" style="width:255px;" >   
        </td>
    </tr>
</table>
        
           
          <label class="control-label labelx"> PIC - KINRA </label>
<table>
    <tr> 
        <td> 
<?php $js = "class='form-control select2_single' id='cpickinra' tabindex='-1' style='width:255px;' "; 
echo form_dropdown('cpickinra', $pic_kinra, isset($default['pic_kinra']) ? $default['pic_kinra'] : '', $js); ?>
        </td>
    </tr>
    <tr>
        <td> <br/>
<input type="text" class="form-control" id="tpickinra" name="tpickinra" style="width:255px;" >   
        </td>
    </tr>
</table>        
        
    </div>
<!-- payment method -->
        
<!-- payment method2 -->
    <div class="col-md-2 col-sm-12 col-xs-12">
          <label class="control-label labelx"> QC-FFA </label>
          <input type="text" class="form-control" id="" name="tqcffa" title="" value="<?php echo isset($default['ffa']) ? $default['ffa'] : '' ?>">
          
          <label class="control-label labelx"> QC-M </label>        
<input type="text" class="form-control" id="" name="tqcm" title="" value="<?php echo isset($default['m']) ? $default['m'] : '' ?>">
        
         <label class="control-label labelx"> QC-I </label>        
<input type="text" class="form-control" id="" name="tqci" title="" value="<?php echo isset($default['i']) ? $default['i'] : '' ?>">
        
 <label class="control-label labelx"> QC-Status </label>
  <select class="form-control" name="cqcstatus">
      <option value="0"<?php echo set_select('cqcstatus', '0', isset($default['qcstatus']) && $default['qcstatus'] == '0' ? TRUE : FALSE); ?>> OK </option>
      <option value="1"<?php echo set_select('cqcstatus', '1', isset($default['qcstatus']) && $default['qcstatus'] == '1' ? TRUE : FALSE); ?>> NOT OK </option>
      <option value="2"<?php echo set_select('cqcstatus', '2', isset($default['qcstatus']) && $default['qcstatus'] == '2' ? TRUE : FALSE); ?>> REJECT </option>
<!--
      <option value="0"> OK </option>
      <option value="1"> NOT OK </option>
      <option value="2"> REJECT </option>
-->
  </select>
        
          <label class="control-label labelx"> PIC-QC </label>
<table>
    <tr> 
        <td> 
<?php $js = "class='form-control select2_single' id='cpicqc' tabindex='-1' style='width:255px;' "; 
echo form_dropdown('cpicqc', $pic_qc, isset($default['pic_qc']) ? $default['pic_qc'] : '', $js); ?>
        </td>
    </tr>
    <tr>
        <td> <br/>
<input type="text" class="form-control" id="tpicqc" name="tpicqc" style="width:255px;" >   
        </td>
    </tr>
</table>  
          
    </div>
<!-- payment method2 -->
        
        
<!-- div alamat penagihan -->
       <div class="col-md-4 col-sm-12 col-xs-12">
               
          <label class="control-label labelx"> Description </label>
<textarea id="" name="tdesc" style="width:100%;" rows="2"><?php echo isset($default['desc']) ? $default['desc'] : '' ?></textarea>     
       </div>
<!-- div alamat penagihan -->

</div>
<!-- form atas   -->
      
      <div class="ln_solid"></div>
      <div class="form-group">
        <div class="col-md-4 col-sm-3 col-xs-12 col-md-offset-10">
          <div class="btn-group">    
          <button type="submit" class="btn btn-success" id="button"> Save </button>
          <button type="reset" class="btn btn-danger" id=""> Cancel </button>
          <?php echo $button_validate; ?>
          </div>
        </div>
      </div>
      
	</form>
      
    <!-- end div layer 1 -->
      
<!-- form transaction table  -->
      
<div class="row">
    
    <div class="col-md-12 col-sm-12 col-xs-12">
      
        <h3> Contract / Origin List </h3> <hr/>
    
 <!-- searching form -->
           
   <form id="ajaxtransform1" class="form-inline" method="post" action="<?php echo $form_action_item; ?>">
      <div class="form-group">
        <label class="control-label labelx"> Contract / Origin </label> <br>
           <input id="titem" class="form-control col-md-6 col-xs-12" type="text" readonly name="titem" required style="width:230px;">
           <?php echo anchor_popup(site_url("registration/get_list/titem/"), '[ ... ]', $atts1); ?>
          &nbsp;
          <input type="hidden" name="hid" value="<?php echo $uid; ?>">
      </div>
      
      <div class="form-group">
        <label class="control-label labelx"> Contract-Qty </label> <br>
        <input type="number" name="tcontractqty" id="tqty" class="form-control" style="width:150px;" maxlength="5" required value="0"> &nbsp;
      </div>   
       
      <div class="form-group">
        <label class="control-label labelx"> Outstanding-Qty </label> <br>
        <input type="number" name="toustandingqty" id="tamount" class="form-control" style="width:150px;" maxlength="10" required value="0"> &nbsp;
      </div>
       
      <div class="form-group">
        <label class="control-label labelx"> Transfer-Qty </label> <br>
         <input type="number" name="ttransferqty" id="tamount" class="form-control" style="width:150px;" maxlength="10" required value="0"> &nbsp;
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
            <th class="column-title"> Origin </th>
            <th class="column-title"> Contract Qty </th>
            <th class="column-title"> Outstanding Qty </th>
            <th class="column-title"> Transfer Qty </th>
            <th class="column-title no-link last"><span class="nobr">Action</span>
            </th>
            <th class="bulk-actions" colspan="7">
              <a class="antoo" style="color:#fff; font-weight:500;">Bulk Actions ( <span class="action-cnt"> </span> ) <i class="fa fa-chevron-down"></i></a>
            </th>
          </tr>
        </thead>

        <tbody>
            
        <?php
             
            if ($items)
            {
                $i=1;
                foreach($items as $res)
                {
                    echo "
                     <tr class=\"even pointer\">
                        <td> ".$i." </td>
                        <td>".$res->origin_no."</td>
                        <td>".num_format($res->contract_amount)."</td>
                        <td>".num_format($res->outstanding_amount)."</td>
                        <td>".num_format($res->transfer_amount)."</td>
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
        
        <h3> Tank Sounding </h3> <hr/>
    
 <!-- searching form -->
           
   <form id="ajaxtransform2" class="form-inline" method="post" action="<?php echo $form_action_sounding; ?>">
       
      <div class="form-group">
        <label class="control-label labelx"> Tank Type </label> <br>
           <select class="form-control" name="ctanktype">
            <option value="SOURCE">SOURCE</option>
            <option value="DEST">DEST</option>
           </select>
          <input type="hidden" name="hid" value="<?php echo $uid; ?>">
      </div>
       
       <div class="form-group">
        <label class="control-label labelx"> Period Type </label> <br>
           <select class="form-control" name="cperiodtype">
            <option value="BEFORE">BEFORE</option>
            <option value="AFTER">AFTER</option>
           </select>
          <input type="hidden" name="hid" value="<?php echo $uid; ?>">
      </div>
      
      <div class="form-group">
        <label class="control-label labelx"> Sounding (Cm) </label> <br>
        <input type="text" name="tsounding" id="" class="form-control" style="width:150px;" maxlength="5" required value="0"> &nbsp;
      </div>   
       
      <div class="form-group">
        <label class="control-label labelx"> Temp (&#8226;C) </label> <br>
        <input type="number" name="ttemp" id="" class="form-control" style="width:150px;" maxlength="10" required value="0"> &nbsp;
      </div>
       
      <div class="form-group">
        <label class="control-label labelx"> Tonase (Kg) </label> <br>
         <input type="text" name="ttonase" id="" class="form-control" style="width:150px;" maxlength="10" required value="0"> &nbsp;
      </div>

      <div class="form-group"> <br>
       <div class="btn-group">    
       <button type="submit" class="btn btn-primary button_inline"> Post </button>
       <button type="button" onClick="" class="btn btn-danger button_inline"> Reset </button>
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
            <th class="column-title"> Type </th>
            <th class="column-title"> Source <br/> Sounding (Cm) </th>
            <th class="column-title"> Dest <br/> Sounding (Cm) </th>
            <th class="column-title"> Source <br/> Temp (&#8226;C) </th>
            <th class="column-title"> Dest <br/> Temp (&#8226;C) </th>
            <th class="column-title"> Source <br/> Tonase (Kg) </th>
            <th class="column-title"> Dest <br/> Tonase (Kg) </th>
            <th class="column-title no-link last"><span class="nobr">Action</span>
            </th>
            <th class="bulk-actions" colspan="7">
<a class="antoo" style="color:#fff; font-weight:500;">Bulk Actions ( <span class="action-cnt"> </span> ) <i class="fa fa-chevron-down"></i></a>
            </th>
          </tr>
        </thead>

        <tbody>
            
        <?php
            
            if ($items_sounding)
            {
                $i=1;
                foreach($items_sounding as $res)
                {
                    echo "
                     <tr class=\"even pointer\">
                        <td> ".$i." </td>
                        <td>".$res->type."</td>
                        <td>".$res->source_cm."</td>
                        <td>".$res->to_cm."</td>
                        <td>".$res->source_temp."</td>
                        <td>".$res->to_temp."</td>
                        <td>".$res->source_tonase."</td>
                        <td>".$res->to_tonase."</td>
<td class=\" last\"> 
<a class=\"btn btn-danger btn-xs text-remove-sounding\" id=\"".$res->id."\"> <i class=\"fa fas-2x fa-trash\"> </i> </a> 
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
    
<!-- kolom total -->
    <div class="col-md-3 col-sm-12 col-xs-12 col-md-offset-9">
        
        <table id="table_summary" style="width:100%; font-size:16px;">
            <tr> 
        <td> Qty Sent (Kg) </td> <td class="amt"> <?php echo isset($qty_kirim) ? $qty_kirim : '0'; ?>   </td> 
            </tr>
<tr> <td> Qty Receive (Kg) </td> <td class="amt"> <?php echo isset($qty_terima) ? $qty_terima : '0'; ?> </td> </tr>
             <tr> 
        <td> Difference (Kg) </td> <td class="amt"> <?php echo isset($selisih) ? $selisih : '0' ?> </td> </tr>
<tr> <td> <h3 style="color:#337AB7; font-weight:bold;"> Percentage </h3> </td> 
     <td class="amt"> <h4 style="color:#337AB7; font-weight:bold;"> <?php echo isset($persentase) ? $persentase : '0' ?> &#37; </h4> </td> </tr>
        </table>
        
    </div>
<!-- kolom total -->
    
</div>
<!-- form transaction table2  -->  
        
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
    
    
