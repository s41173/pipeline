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
			<tr> <td> Period </td> <td> : </td> <td> <?php echo $start.' - '.$end; ?> </td> </tr>
            <tr> <td> Branch </td> <td> : </td> <td> <?php echo $branch; ?> </td> </tr>
            <tr> <td> Paid Status </td> <td> : </td> <td> <?php echo $paid; ?> </td> </tr>
            <tr> <td> Confirmation Status </td> <td> : </td> <td> <?php echo $confirm; ?> </td> </tr>
			<tr> <td> Run Date </td> <td> : </td> <td> <?php echo $rundate; ?> </td> </tr>
			<tr> <td> Log </td> <td> : </td> <td> <?php echo $log; ?> </td> </tr>
		</table>
	</div>

	<center>
	   <div style="border:0px solid green; width:230px;">
	      <h4> <?php echo isset($company) ? $company : ''; ?> <br> Sales - Report (Pivot Table) </h4>
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
<th> No </th> <th> Branch </th> <th> Code </th> <th> Date </th> <th> Manufacture </th> <th> Category </th> <th> SKU </th> <th> Product </th> <th> Qty </th> <th> Amount </th> <th> User </th> <th> Confirmation </th> 
           </tr>
           </thead>
		  
          <tbody> 
		  <?php 
              
              function payment($val)
              {
                  $res = new Payment_lib(); 
                  return strtoupper($res->get_name($val));
              }
              
              function pstatus($val){ if ($val == 0){ return 'N'; }else{ return 'Y'; } }
              
              function branch($val){
                  $br = new Branch_lib();
                  return $br->get_name($val);
              }
              
              function user($val){
                  $br = new Admin_lib();
                  return $br->get_username($val);
              }
              
			  		  
		      $i=1; 
			  if ($reports_item)
			  {
				foreach ($reports_item as $res)
				{	
				   echo " 
				   <tr> 
				       <td class=\"strongs\">".$i."</td> 
                       <td class=\"strongs\">".branch($res->branch_id)."</td> 
                       <td class=\"strongs\">".$res->orderid."</td> 
                       <td class=\"strongs\">".tglin($res->dates)."</td> 
                       <td class=\"strongs\">".strtoupper($res->manufacture)."</td>
                       <td class=\"strongs\">".strtoupper($res->category)."</td>
                       <td class=\"strongs\">".$res->sku."</td>
                       <td class=\"strongs\">".strtoupper($res->name)."</td>
                       <td class=\"strongs\">".$res->qty."</td>
                       <td class=\"strongs\">".intval($res->qty*$res->price)."</td>
                       <td class=\"strongs\">".user($res->userid)."</td>
                       <td class=\"strongs\">".pstatus($res->confirmation)."</td>
				   </tr>";
				   $i++;
				}
			 }  
		  ?>
              
    <tr>
        <td></td> <td> Total Bruto : </td> <td> <?php echo idr_format(intval($total_sum['discount']+$total_sum['total'])) ?> </td>
    </tr>
              
    <tr>
        <td></td> <td> Total Discount : </td> <td> <?php echo idr_format(intval($total_sum['discount'])) ?> </td>
    </tr>
              
    <tr>
       <td></td> <td> Total Net : </td> <td> <?php echo idr_format(intval($total_sum['total'])) ?> </td>
    </tr>
              
		</tbody>      
		</table>
        
	</div>
	
     <a style="float:left; margin:10px;" title="Back" href="<?php echo site_url('sales'); ?>"> 
        <img src="<?php echo base_url().'images/back.png'; ?>"> 
     </a>
    
</div>

</body>
</html>
