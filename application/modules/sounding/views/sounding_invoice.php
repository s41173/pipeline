<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title> Sounding Voucher - <?php echo $codetrans; ?></title>
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
	
	<h2 style="font-size:18px; font-weight:normal; text-align:center; text-decoration:underline;"> SOUNDING VOUCHER </h2> 
    <div style="clear:both; "></div> 
	
	<div style="width:350px; border:0px solid #000; float:left;">
		<table style="font-size:11px;">
            <tr> <td> Doc-No </td> <td>:</td> <td> <?php echo $codetrans; ?> </td> </tr>
			<tr> <td> Tank </td> <td>:</td> <td> <?php echo $tank; ?> </td> </tr>
		</table>
	</div>
	
	<div style="width:200px; border:0px solid red; float:right;">
		<table style="font-size:11px;">
			<tr> <td> Date </td> <td>:</td> <td> <?php echo tglin($dates); ?> </td> </tr>
			<tr> <td> Notes </td> <td>:</td> <td> <?php echo $notes; ?> </td> </tr>
		</table>
	</div>
	
	<div style="clear:both; "></div>
	
	<div style="margin:3px 0 0 0; border-bottom:0px dotted #000;">
		
		<table class="product">

<tr> <th> Attribute </th> <th> Value </th> </tr>
		 
<tr> <td class="left"> Sounding (cm) </td> <td class="right"> <?php echo $after_corr; ?> </td> </tr>
<tr> <td class="left"> Temperature (&#8451;) </td> <td class="right"> <?php echo $temperature; ?> </td> </tr>
<tr> <td class="left"> Density </td> <td class="right"> <?php echo $density; ?> </td> </tr>
<tr> <td class="left"> Coeff </td> <td class="right"> <?php echo $coeff; ?> </td> </tr>
<tr> <td class="left"> Obv </td> <td class="right"> <?php echo $obv; ?> </td> </tr>
<tr> <td class="left"> Adj Kg </td> <td class="right"> <?php echo $adj; ?> </td> </tr>
<tr> <td class="left"> Net Kg </td> <td class="right"> <?php echo $netkg; ?> </td> </tr>
<tr> <td class="left"> FFA (%) </td> <td class="right"> <?php echo $ffa ?> </td> </tr>
<tr> <td class="left"> Moisture (%) </td> <td class="right"> <?php echo $moisture; ?> </td> </tr>
<tr> <td class="left"> Dirt (%) </td> <td class="right"> <?php echo $dirt; ?> </td> </tr>
		 		 			
		</table>
		
		
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
