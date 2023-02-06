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

<script src="<?php echo base_url(); ?>js/moduljs/qc.js"></script>
<script src="<?php echo base_url(); ?>js-old/register.js"></script>

<script type="text/javascript">

	var sites_add  = "<?php echo site_url('qc/add_process/');?>";
	var sites_edit = "<?php echo site_url('qc/update_process/');?>";
	var sites_del  = "<?php echo site_url('qc/delete/');?>";
	var sites_get  = "<?php echo site_url('qc/update/');?>";
    var sites  = "<?php echo site_url('qc/');?>";
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
                   <label class="control-label labelx"> Transaction Date </label>
    <input type="text" title="Date" class="form-control" id="ds5" name="tdate" required readonly
           value="<?php echo isset($default['date']) ? $default['date'] : '' ?>" style="width:160px" /> 
               </div>
               
           </div>
           
       </div>
<!-- div untuk account place  -->


</div>
<!-- form atas   -->      
	</form>
      
    <!-- end div layer 1 -->
      
<!-- form transaction table  -->
      
<div class="row">
    
    <div class="col-md-12 col-sm-12 col-xs-12">
      
        <h3> QC - List </h3> <hr/>
    
 <!-- searching form -->
           
   <form id="ajaxtransform1" class="" method="post" action="<?php echo $form_action_item; ?>">
     <div class="form-inline"> 
      <div class="form-group">
        <label class="control-label labelx"> Contract / Origin </label> <br>
<?php $js = "class='form-control select2_single' id='ccontract' tabindex='-1' style='width:350px;' "; 
echo form_dropdown('ccontract', $contract, isset($default['contract']) ? $default['contract'] : '', $js); ?> &nbsp;
          <input type="hidden" name="hid" value="">
      </div>
       
      <div class="form-group">
        <label class="control-label labelx"> Supplier </label> <br>
<?php $js = "class='form-control select2_single' id='csupplier' tabindex='-1' style='width:200px;' "; 
echo form_dropdown('csupplier', $supplier, isset($default['supplier']) ? $default['supplier'] : '', $js); ?> 
          
<input type="text" name="tsupplier" id="tsupplier" class="form-control" style="width:180px;">
  
      </div>
      
      <div class="form-group">
        <label class="control-label labelx"> NO-GK </label> <br>
        <input type="text" name="tnogk" id="tnogk" class="form-control" style="width:150px;" required> &nbsp;
      </div>   
         
      <div class="form-group">
        <label class="control-label labelx"> Driver </label> <br>
        <input type="text" name="tdriver" id="tdriver" class="form-control" style="width:150px;" required> &nbsp;
      </div>   
         
      <div class="form-group">
        <label class="control-label labelx"> Ticket-No </label> <br>
        <input type="text" name="tticket" id="tticket" class="form-control" style="width:150px;" required> &nbsp;
      </div>   
       
    </div>  <br/>
<!--  batas inline 1  -->
       
     <!-- batas inline 2 -->
     <fieldset> <legend> From Analysis </legend>
     <div class="form-inline">
         
       <div class="form-group">
        <label class="control-label labelx"> Bruto </label> <br>
        <input type="number" name="tbruto_from" id="tbruto_from" class="form-control" style="width:100px;" maxlength="10" required value="0"> &nbsp;
      </div>
       
      <div class="form-group">
        <label class="control-label labelx"> Tara </label> <br>
         <input type="number" name="ttara_from" id="ttara_from" class="form-control" style="width:100px;" maxlength="10" required value="0"> &nbsp;
      </div>
       
       <div class="form-group">
        <label class="control-label labelx"> Netto </label> <br>
         <input type="number" name="tnetto_from" id="tnetto_from" class="form-control" style="width:100px;" maxlength="10" required value="0"> &nbsp;
      </div>     
            
      <div class="form-group">
        <label class="control-label labelx"> FFA-(%) </label> <br>
        <input type="text" name="tffa_from" id="tffa_from" class="form-control" style="width:80px;" maxlength="10" required value="0"> &nbsp;
      </div>
       
      <div class="form-group">
        <label class="control-label labelx"> MOIST-(%) </label> <br>
         <input type="text" name="tmoist_from" id="tmoist_from" class="form-control" style="width:80px;" maxlength="10" required value="0"> &nbsp;
      </div>
       
       <div class="form-group">
        <label class="control-label labelx"> IMP-(%) </label> <br>
         <input type="text" name="timp_from" id="timp_from" class="form-control" style="width:80px;" maxlength="10" required value="0"> &nbsp;
      </div>
         
      <div class="form-group">
        <label class="control-label labelx"> IV-(%) </label> <br>
         <input type="text" name="tiv_from" id="tiv_from" class="form-control" style="width:80px;" maxlength="10" required value="0"> &nbsp;
      </div>
         
      <div class="form-group">
        <label class="control-label labelx"> MPT-(%) </label> <br>
         <input type="text" name="tmpt_from" id="tmpt_from" class="form-control" style="width:80px;" maxlength="10" required value="0"> &nbsp;
      </div>
         
      <div class="form-group">
        <label class="control-label labelx"> Color-(%) </label> <br>
         <input type="text" name="tcolor_from" id="tcolor_from" class="form-control" style="width:80px;" maxlength="10" required value="0"> &nbsp;
      </div>
         
     </div>
     </fieldset>  <br/> 
     <!-- batas inline 2 -->   
    
     <!-- batas inline 3 -->
     <fieldset> <legend> Real Analysis </legend>
     <div class="form-inline">
         
       <div class="form-group">
        <label class="control-label labelx"> Bruto </label> <br>
        <input type="number" name="tbruto" id="tbruto" class="form-control" style="width:80px;" maxlength="10" required value="0"> &nbsp;
      </div>
       
      <div class="form-group">
        <label class="control-label labelx"> Tara </label> <br>
         <input type="number" name="ttara" id="ttara" class="form-control" style="width:80px;" maxlength="10" required value="0"> &nbsp;
      </div>
       
       <div class="form-group">
        <label class="control-label labelx"> Netto </label> <br>
         <input type="number" name="tnetto" id="tnetto" class="form-control" style="width:80px;" maxlength="10" required value="0"> &nbsp;
      </div>     
            
      <div class="form-group">
        <label class="control-label labelx"> FFA-(%) </label> <br>
        <input type="text" name="tffa" id="tffa" class="form-control" style="width:80px;" maxlength="10" required value="0"> &nbsp;
      </div>
       
      <div class="form-group">
        <label class="control-label labelx"> MOIST-(%) </label> <br>
         <input type="text" name="tmoist" id="tmoist" class="form-control" style="width:80px;" maxlength="10" required value="0"> &nbsp;
      </div>
       
       <div class="form-group">
        <label class="control-label labelx"> IMP-(%) </label> <br>
         <input type="text" name="timp" id="timp" class="form-control" style="width:80px;" maxlength="10" required value="0"> &nbsp;
      </div>
         
      <div class="form-group">
        <label class="control-label labelx"> IV-(%) </label> <br>
         <input type="text" name="tiv" id="tiv" class="form-control" style="width:80px;" maxlength="10" required value="0"> &nbsp;
      </div>
         
      <div class="form-group">
        <label class="control-label labelx"> MPT-(%) </label> <br>
         <input type="text" name="tmpt" id="tmpt" class="form-control" style="width:80px;" maxlength="10" required value="0"> &nbsp;
      </div>
         
      <div class="form-group">
        <label class="control-label labelx"> Color-(%) </label> <br>
         <input type="text" name="tcolor" id="tcolor" class="form-control" style="width:80px;" maxlength="10" required value="0"> &nbsp;
      </div>
         
     </div>
     </fieldset> <br/>  
     <!-- batas inline 3 -->   
       
     <!-- batas inline 4 -->
     <fieldset> 
     <div class="form-inline">
         
      <div class="form-group">
        <label class="control-label labelx"> Description </label> <br>
         <input type="text" name="tdesc" id="tdesc" class="form-control" style="width:250px;" value=""> &nbsp;
      </div>
         
      <div class="form-group">
        <label class="control-label labelx"> Vendor Out </label> <br>
        <input type="text" class="form-control" name="tdatefrom" id="ds5" value=""> 
      </div>
         
      <div class="form-group">
        <label class="control-label labelx"> Incoming Dry Port </label> <br>
        <input type="text" class="form-control" name="tdateincoming" id="ds6" value=""> 
      </div>
         
      <div class="form-group">
        <label class="control-label labelx"> Outgoing Dry Port </label> <br>
        <input type="text" class="form-control" name="tdateoutcoming" id="ds7" value=""> 
      </div>

      <div class="form-group"> <br>
       <div class="btn-group">    
       <button type="submit" class="btn btn-primary button_inline"> Post </button>
       <button type="button" onClick="load_data();" class="btn btn-danger button_inline"> Reset </button>
       </div>
      </div>
         
     </div>
     </fieldset> <br/>  
     <!-- batas inline 4 -->   
  
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
            <th class="column-title"> Supplier </th>
            <th class="column-title"> GK-No </th>
            <th class="column-title"> Bruto </th>
            <th class="column-title"> Tara </th>
            <th class="column-title"> Netto </th>
            <th class="column-title"> Ffa </th>
            <th class="column-title"> Moist </th>
            <th class="column-title"> Imp </th>
            <th class="column-title"> Iv </th>
            <th class="column-title"> Mpt </th>
            <th class="column-title"> Color </th>
            <th class="column-title no-link last"><span class="nobr">Action</span>
            </th>
            <th class="bulk-actions" colspan="7">
              <a class="antoo" style="color:#fff; font-weight:500;">Bulk Actions ( <span class="action-cnt"> </span> ) <i class="fa fa-chevron-down"></i></a>
            </th>
          </tr>
        </thead>

        <tbody>
            
        <?php
            
            function get_origin($val){
                $contract = new Contract_lib();
                $result = $contract->get_details($val);
                return $result->origin_no;
            }
             
            if ($items)
            {
                $i=1;
                foreach($items as $res)
                {
                    echo "
                     <tr class=\"even pointer\">
                        <td> ".$i." </td>
                        <td>".get_origin($res->contract_id)."</td>
                        <td>".strtoupper($res->supplier).'<br/>'.ucfirst($res->description)."</td>
                    <td>".strtoupper($res->gk_no).'<br/>'.strtoupper($res->driver)."<br/>".$res->ticket_no."</td>
                        <td>".number_format($res->bruto_from).'<br/>'.number_format($res->bruto)."</td>
                        <td>".number_format($res->tara_from).'<br/>'.number_format($res->tara)."</td>
                        <td>".number_format($res->netto_from).'<br/>'.number_format($res->netto)."</td>
                        <td>".$res->ffa_from.'<br/>'.$res->ffa."</td>
                        <td>".$res->moist_from.'<br/>'.$res->moist."</td>
                        <td>".$res->imp_from.'<br/>'.$res->imp."</td>
                        <td>".$res->iv_from.'<br/>'.$res->iv."</td>
                        <td>".$res->mpt_from.'<br/>'.$res->mpt."</td>
                        <td>".$res->color_from.'<br/>'.$res->color."</td>
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
    
    
