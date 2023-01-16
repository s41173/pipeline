<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title> Shore Calculation Voucher - <?php echo $codetrans; ?></title>
<style media="all">

	#logo { margin:0 0 0 75px;}
	#logotext{ font-size:12px; text-align:center; margin:0; }
	p { margin:0; padding:0; font-size:11px;}
	#pono{ font-size:18px; padding:0; margin:0 5px 10px 0; text-align:left;}
	
	table.product
	{ border-collapse:collapse; width:100%; }
	
	table.product,table.product th
	{	border: 1px solid black; font-size:13px; font-weight:bold; padding:4px 0 4px 0; }
	
	table.product,table.product td
	{	border: 1px solid black; font-size:12px; font-weight:normal; padding:3px 0 3px 0; text-align:center; }
	
	table.product td.left { text-align:left; padding:3px 5px 3px 10px; }
	table.product td.right { text-align:right; padding:3px 10px 3px 5px; }
	
</style>
    
<script type="text/javascript">
    function closeWindow() {
        setTimeout(function() {
        window.close();
        }, 60000);
        }
</script>    
</head>

<body onLoad="closeWindow()">

<div style="width:750px; font-family:Arial, Helvetica, sans-serif; font-size:12px;"> 
	
	<h2 style="font-size:18px; font-weight:normal; text-align:center; text-decoration:underline;"> Shore Calculation Voucher </h2> 
    <div style="clear:both; "></div> 
	
	<div style="width:350px; border:0px solid #000; float:left;">
		<table style="font-size:11px;">
            <tr> <td> Doc-No </td> <td>:</td> <td> <?php echo $codetrans; ?> </td> </tr>
            <tr> <td> Instruction-No </td> <td>:</td> <td> <?php echo $instructionno; ?> </td> </tr>
            <tr> <td> Transaction Type </td> <td>:</td> <td> <?php echo $type; ?> </td> </tr>
			<tr> <td> Source Tank </td> <td>:</td> <td> <?php echo $source; ?> </td> </tr>
            <tr> <td> Dest Tank </td> <td>:</td> <td> <?php echo $vessel; ?> </td> </tr>
		</table>
	</div>
	
	<div style="width:200px; border:0px solid red; float:right;">
		<table style="font-size:11px;">
			<tr> <td> Date </td> <td>:</td> <td> <?php echo tglin($dates); ?> </td> </tr>
			<tr> <td> Notes / Remarks </td> <td>:</td> <td> <?php echo $notes; ?> </td> </tr>
            <tr> <td> Consignee </td> <td>:</td> <td> <?php echo $cust_id; ?> </td> </tr>
            <tr> <td> Commodity </td> <td>:</td> <td> <?php echo $content; ?> </td> </tr>
            <tr> <td> Log </td> <td>:</td> <td> <?php echo $log; ?> </td> </tr>
		</table>
	</div>
	
	<div style="clear:both; "></div>
	
	<div style="margin:3px 0 0 0; border-bottom:0px dotted #000;">
		
       <h3> Transaction Details </h3>
       <table class="product">

<tr> <th> Attribute </th> <th> Value </th> </tr>
		 
<tr> <td class="left"> Fuel </td> <td class="right"> <?php echo $fuel; ?> </td> </tr>
<tr> <td class="left"> Use Oil Boom </td> <td class="right"> <?php echo $oil_boom; ?> </td> </tr>
<tr> <td class="left"> ETA </td> <td class="right"> <?php echo $eta; ?> </td> </tr>
<tr> <td class="left"> ETB </td> <td class="right"> <?php echo $etb; ?> </td> </tr>
<tr> <td class="left"> Laycan </td> <td class="right"> <?php echo $laycan; ?> </td> </tr>
<tr> <td class="left"> Until </td> <td class="right"> <?php echo $until; ?> </td> </tr>
<tr> <td class="left"> Heating Start </td> <td class="right"> <?php echo $heating; ?> </td> </tr>
<tr> <td class="left"> Heating Until </td> <td class="right"> <?php echo $heating_until ?> </td> </tr>
<tr> <td class="left"> Comm. Pumping </td> <td class="right"> <?php echo $comm_pumping; ?> </td> </tr>
<tr> <td class="left"> Comm. Pumping </td> <td class="right"> <?php echo $comp_pumping; ?> </td> </tr>
           
<tr> <td class="left"> Shore Line Cond </td> <td class="right"> <?php echo $shore_line_cond ?> </td> </tr>
<tr> <td class="left"> Before Load </td> <td class="right"> <?php echo $before_load; ?> </td> </tr>
<tr> <td class="left"> Cleaning System </td> <td class="right"> <?php echo $cleaning_sys; ?> </td> </tr>
<tr> <td class="left"> After Load </td> <td class="right"> <?php echo $after_load ?> </td> </tr>
           
<tr> <td class="left"> Ship Name </td> <td class="right"> <?php echo $ship_name; ?> </td> </tr>
<tr> <td class="left"> Ship Rep </td> <td class="right"> <?php echo $comp_pumping; ?> </td> </tr>
<tr> <td class="left"> Ship Company </td> <td class="right"> <?php echo $ship_company; ?> </td> </tr>
<tr> <td class="left"> Buyer Name </td> <td class="right"> <?php echo $buyer_name; ?> </td> </tr>
<tr> <td class="left"> Buyer Rep </td> <td class="right"> <?php echo $buyer_rep; ?> </td> </tr>
<tr> <td class="left"> Buyer Company </td> <td class="right"> <?php echo $buyer_company; ?> </td> </tr>
		 		 			
		</table> <br>
        
<h3> Sounding Transaction </h3>
		<table class="product">

<tr> <th> Attribute </th> <th> Before </th> <th> After </th> </tr>
		 
<tr> <td class="left"> Sounding (cm) </td> <td class="right"> <?php echo $tacorr_input; ?> </td> <td class="right"> <?php echo $tacorr_output; ?> </td> 
</tr>
            
<tr> <td class="left"> Temperature (&#8451;) </td> <td class="right"> <?php echo $ttemp_input; ?> </td> 
<td class="right"> <?php echo $ttemp_output; ?> </td> 
</tr>
            
<tr> <td class="left"> Density </td> <td class="right"> <?php echo $tdensity_input; ?> </td> 
<td class="right"> <?php echo $tdensity_output; ?> </td> 
</tr>
<tr> <td class="left"> Coeff </td> <td class="right"> <?php echo $tcoeff_input; ?> </td> 
     <td class="right"> <?php echo $tcoeff_output; ?> </td> 
</tr>
<tr> <td class="left"> Obv </td> <td class="right"> <?php echo $tobv_input; ?> </td>
     <td class="right"> <?php echo $tobv_output; ?> </td>
</tr>
<tr> <td class="left"> Adj Kg </td> <td class="right"> <?php echo $tadj_input; ?> </td>
     <td class="right"> <?php echo $tadj_output; ?> </td>
</tr>
<tr> <td class="left"> Net Kg </td> <td class="right"> <?php echo $tnetkg_input; ?> </td> 
     <td class="right"> <?php echo $tnetkg_output; ?> </td> 
</tr>
<tr> <td class="left"> FFA (%) </td> <td class="right"> <?php echo $tffa_input ?> </td> 
     <td class="right"> <?php echo $tffa_output ?> </td> 
</tr>
<tr> <td class="left"> Moisture (%) </td> <td class="right"> <?php echo $tmoisture_input; ?> </td> 
     <td class="right"> <?php echo $tmoisture_output; ?> </td> 
</tr>
<tr> <td class="left"> Dirt (%) </td> <td class="right"> <?php echo $tdirt_input; ?> </td> 
     <td class="right"> <?php echo $tdirt_output; ?> </td> 
</tr> 	

<tr> <td class="left"> Diff OBV </td> <td class="right" colspan="2"> <b> <?php echo $diff_obv; ?> </b> </td> </tr>
<tr> <td class="left"> Diff Netkg </td> <td class="right" colspan="2"> <b> <?php echo $diff_net; ?> </b> </td> </tr>
		</table> <br>
		
		
		<div style="clear:both; "></div> 
		
		<div style="width:620px; border:0px solid #000; float:right; margin:3px 0px 0 0;">
		<style>
			.sig{ font-size:11px; width:100%; float:right; text-align:center;}
			.sig td{ width:155px;}
		</style>
			<table border="0" class="sig">
				<tr> <td> Approved By : </td> <td> Reviewed By : </td> <td> Prepared By : </td> </tr>
			</table> <br> <br> <br> <br> <br> 
			
			<table border="0" class="sig">
				<tr> <td> Manager </td> <td> Accounting </td> <td> (<?php echo $user; ?>) </td> </tr>
			</table>
		</div>
		
		<!--<div style="float:right;">
			
			<table>
				<p> &nbsp; &nbsp; Dipesan Oleh, &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Disetujui Oleh, </p> <br> <br> <br> <br>
				<p style="text-align:right;"> ( <?php echo $user; ?> ) &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; (_______________) </p>
				<p> &nbsp; &nbsp; &nbsp; Purchasing  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  Direktur Utama </p>
			</table>
			<br>
		</div> -->
	
</div>
    </div>
</body>
</html>
