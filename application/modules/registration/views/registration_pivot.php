<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="<?php echo base_url().'images/fav_icon.png';?>" >
<title> <?php echo isset($title) ? $title : ''; ?>  </title>
<style media="all">
	table{ font-family:"Arial", Times, serif; font-size:11px;}
	h4{ font-family:"Arial", Times, serif; font-size:14px; font-weight:600;}
	.clear{clear:both;}
	table th{ background-color:#EFEFEF; padding:4px 0px 4px 0px; border-top:1px solid #000000; border-bottom:1px solid #000000;}
    p{ font-family:"Arial", Times, serif; font-size:12px; margin:0; padding:0;}
	legend{font-family:"Arial", Times, serif; font-size:13px; margin:0; padding:0; font-weight:600;}
	.tablesum{ font-size:13px;}
	.strongs{ font-weight:normal; font-size:12px; border-top:1px dotted #000000; }
	.poder{ border-bottom:0px solid #000000; color:#0000FF; font-size:9pt;}
	.red{ border-bottom:0px solid #000000; color:#900; font-size:10pt;}
</style>

    <link rel="stylesheet" type="text/css" href="<?php echo base_url().'js-old/pivot/' ?>pivot.css">
	  <script type="text/javascript" src="<?php echo base_url().'js-old/pivot/' ?>jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/pivot/' ?>jquery-ui-1.9.2.custom.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/pivot/' ?>jquery.ui.touch-punch.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/pivot/' ?>pivot.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {

			var input = $("#input")
			$("#output").pivotUI(input);
			$("#input").hide();
        });
    </script>

</head>

<body>

<div style="width:100%; border:0px solid blue; font-family:Arial, Helvetica, sans-serif; font-size:12px;">

	<div style="border:0px solid red; float:left;">
		<table border="0">
			<tr> <td> Run Date </td> <td> : </td> <td> <?php echo $rundate; ?> </td> </tr>
			<tr> <td> Log </td> <td> : </td> <td> <?php echo $log; ?> </td> </tr>
		</table>
	</div>

	<center>
	   <div style="border:0px solid green; width:230px;">
	      <h4> <?php echo isset($company) ? $company : ''; ?> <br> Registration - Report (Pivot Table) </h4>
	   </div>
	</center>

	<div class="clear"></div>

	<div style="width:100%; border:0px solid brown; margin-top:20px; border-bottom:0px dotted #000000; ">

    	<div id='jqxWidget'>
        <div style='margin-top: 10px;' id="output"> </div>
        </div>

		<table id="input" border="0" width="100%">
		   <thead>
           <tr>
<th> NO </th> <th> TYPE </th> <th> CODE </th> <th> DATE </th> <th> DOCNO </th> <th> SOURCE </th> <th> TO </th>
<th> PERIOD </th> <th> PIC-IBL </th> <th> PIC-OBL </th> <th> PIC-KINRA </th>
<th> FFA </th> <th> M </th> <th> I </th> <th> PIC-QC </th> <th> QC STATUS </th> <th> SEGEL STATUS </th>
<th> DESCRIPTION </th> <th> RECEIVED </th> <th> VALIDATION </th> <th> POSTED </th> <th> CREATED </th>
		   </tr>
           </thead>
		  
          <tbody> 
		  <?php 
              
function postedstatus($val){ if ($val == 0){ return 'N'; }else{ return 'Y'; } }
function qcstatus($val){ if ($val == 0)
{ return 'OK'; }elseif($val == 1){ return 'NOT OK'; }else{ return "REJECT"; } }

function segelstatus($val){ if ($val == 0){ return 'OK'; }else{ return 'REJECT'; } }
              
function get_qty($regid){
    $sounding = new Tank_sounding_lib();
    return $sounding->get_qty_receive($regid);
}
			  		  
		      $i=1; 
			  if ($reports)
			  {
				foreach ($reports as $res)
				{	
				   echo " 
				   <tr> 
				       <td class=\"strongs\">".$i."</td> 
                       <td class=\"strongs\">".$res->type."</td> 
                       <td class=\"strongs\">".$res->code."</td> 
					   <td class=\"strongs\">".tglincompletetime($res->dates,1)."</td>
                       <td class=\"strongs\">".$res->docno."</td>
                       <td class=\"strongs\">".$res->source_tank."</td>
                       <td class=\"strongs\">".$res->to_tank."</td>
<td class=\"strongs\">".tglincompletetime($res->start_transfer,1).' - '.tglincompletetime($res->end_transfer,1)."</td>
                       <td class=\"strongs\">".$res->pic_1."</td>
                       <td class=\"strongs\">".$res->pic_2."</td>
                       <td class=\"strongs\">".$res->pic_3."</td>
                       <td class=\"strongs\">".$res->qc_ffa."</td>
                       <td class=\"strongs\">".$res->qc_m."</td>
                       <td class=\"strongs\">".$res->qc_i."</td>
                       <td class=\"strongs\">".$res->pic_qc."</td>
                       <td class=\"strongs\">".qcstatus($res->qc_status)."</td>
                       <td class=\"strongs\">".segelstatus($res->segel_status)."</td>
                       <td class=\"strongs\">".$res->description."</td>
                       <td class=\"strongs\">".get_qty($res->id)."</td>
                       <td class=\"strongs\">".postedstatus($res->validation)."</td>
                       <td class=\"strongs\">".postedstatus($res->approved)."</td>
                       <td class=\"strongs\">".tglincompletetime($res->created,1)."</td>
				   </tr>";
				   $i++;
				}
			 }  
		  ?>
		</tbody>      
		</table>
	</div>
	
     <a style="float:left; margin:10px;" title="Back" href="<?php echo site_url('registration'); ?>"> 
        <img src="<?php echo base_url().'images/back.png'; ?>"> 
     </a>
    
</div>

</body>
</html>
