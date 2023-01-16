<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title> <?php echo $p_name; ?> - PO-00<?php echo isset($pono) ? $pono : ''; ?></title>
<style media="all">
	
	body{ font-family:Tahoma, Geneva, sans-serif;}
	#logo { margin:0 10px 5px 10px; float:left; border:0px solid red;}
	#logotext{ font-size:1em; text-align:center; margin:10px 0 0 0px; }
	p { margin:0; padding:0; font-size:1.05em;}
	#pono{ font-size:1.4em; padding:0; margin:0 5px 10px 0; text-align:left;}
	.clear{ clear:both;}
	
	table.product
	{ border-collapse:collapse; width:100%; margin-bottom:5px; }
	
	table.product,table.product th
	{	border: 1px solid black; font-size:1.05em; font-weight:bold; padding:3px 0 3px 0; }
	
	table.product,table.product td
	{	border: 1px solid black; font-size:1.05em; font-weight:normal; padding:3px 0 3px 0; text-align:center; }
	
	table.product td.left { text-align:left; padding:3px 5px 3px 10px; }
	table.product td.right { text-align:right; padding:3px 10px 3px 5px; }
	
</style>
</head>
    
<script type="text/javascript">
    
    function closeWindow() {
        setTimeout(function() {
        window.close();
        }, 300000);
    }
    
</script>   

<body onLoad="closeWindow()">

<div style="width:20.5cm; font-family:Arial, Helvetica, sans-serif; font-size:12px; border:0px solid red;">

	<div style="border:0px solid #000; width:11.5cm; float:left;">
		<img id="logo" align="middle" width="120" height="100" src="<?php echo isset($logo) ? $logo : ''; ?>"> 
		<p id="logotext"> 
		  <?php echo $paddress; ?> <br> Kotamadya Medan - <?php echo $p_zip; ?> &nbsp; Telp. (061) 80026062 <br>
		  E-Mail : info@delicaindonesia.com - Website : www.delicaindonesia.com
		</p>
		<p style="float:left; margin:0 0 10px 70px; padding:5px 0 0 5px; font-weight:bold;"> NPWP : <?php echo $p_npwp; ?> </p>
	</div>
	
	<div style="border:0px solid blue; float:right; max-width:7cm;">
		
		<h3 id="pono"> No : PO-00<?php echo $pono; ?> &nbsp; &nbsp; <?php echo $currency; ?> </h3>
		<p> Medan, &nbsp; <?php echo isset($podate) ? $podate : ''; ?> </p> <br>
		<p> To, </p> 
		<p style="margin:8px 0 0 0;"> <b> <?php echo isset($vendor) ? $vendor : ''; ?> </b> </p>
		<p> <?php echo isset($address) ? $address : ''; ?> - <?php echo isset($city) ? $city : ''; ?> </p> 
		<p> <?php echo isset($phone) ? $phone : ''; ?> </p>
		
	</div>
	
	<div style="clear:both; "></div>
	
	<h2 style="font-size:1.4em; font-weight:normal; text-align:center; margin:5px 0px 10px 0px; padding:0 0 0 25px;"> PURCHASE ORDER </h2> <div style="clear:both; "></div> 

	
	<div style="clear:both; "></div>
	
	<div style="border-bottom:1px dotted #000;">
		<?php //echo ! empty($table) ? $table : ''; ?>
		
		<table style="margin-top:5px;" class="product">

		 <tr> 
			<th> No </th> <th> Product </th> <th> Qty </th> <th> Price </th> <th> Tax </th> <th> Total Amount </th>
		 </tr>
		 
		 <?php
		 	
			if ($items)
			{
function get_sku($pid){ $res = new Product_lib(); return strtoupper($res->get_sku($pid)); }
function get_name($pid,$type='name'){ $res = new Product_lib(); return strtoupper($res->get_name($pid,$type)); }
function get_unit($pid){ $res = new Product_lib(); return $res->get_unit($pid); }
				
				$i=1;
				foreach ($items as $res)
				{
					echo "
					
					 <tr> 
						<td> ".$i." </td>
<td class=\"left\"> ".get_sku($res->product).' - '.get_name($res->product).'<br>'.get_name($res->product,'model')." </td> 
						<td> ".$res->qty.' '.get_unit($res->product)." </td> 
						<td class=\"right\"> ".number_format($res->price, 2, ",", ".")." </td> 
						<td class=\"right\"> ".number_format($res->tax)." </td> 
						<td class=\"right\"> ".number_format($res->amount + $res->tax,2, ",", ".")." </td>   
					 </tr>
					
					"; $i++;
				}
			}
			
		 ?>
		 
		 
<!--		 <tr> 
			<td> 1 </td>
			<td class="left"> Dodol Perbaungan Medan Barat </td> 
			<td> 10 </td> 
			<td class="right"> 10.000 </td> 
			<td class="right"> 1.000 </td> 
			<td class="right"> 11.000 </td>   
		 </tr> -->

<tr> <td></td> <td class="left"> Landed Cost </td> <td colspan="3"></td>   <td class="right"> <?php echo number_format($cost,2, ",", "."); ?> </td> </tr>
<tr> <td></td> <td class="left"> Credit / Debit </td> <td colspan="3"> <?php echo $ap_over; ?> </td>  <td class="right"> (<?php echo number_format($over,2, ",", "."); ?>) </td> </tr>
<tr> <td></td> <td class="left"> Down Payment </td> <td colspan="3"></td>   <td class="right"> (<?php echo number_format($p1,2, ",", "."); ?>) </td> </tr>
		 <tr> <td colspan="5"></td>  <td class="right"> <?php echo number_format($p2,2, ",", "."); ?> </td> </tr>
			
		</table>
		
		<div style="float:left; width:7.5cm; border:0px solid #000;">  
			<p style="margin:0; padding:2px 0 0 0; font-style:italic; font-size:13px;"> In words :#<?php echo ucfirst($terbilang); ?># </p>
            <p style="margin:0; padding:5px 0 0 0;"> Description : <?php echo $desc; ?> / Log : <?php echo $log; ?> </p>
		</div>
		
		<div style="float:right;">
			
            <style>
			.sig{ font-size:12px; width:100%; float:right; text-align:center;}
			.sig td{ width:155px;}
		    </style>
            
			<table border="0" class="sig">
				<tr> <td> Approved By : </td> <td> Review By : </td> <td> Ordered By: </td> </tr>
			</table> <br> <br> <br> <br> <br> 
			
			<table border="0" class="sig">
				<tr> <td> Manager </td> <td> Accounting </td> <td> Purchasing <br> (<?php echo $user; ?>) </td> </tr>
			</table>
            
			<br>
		</div>
		
		<div style="clear:both; ">
		
	</div>	
	
</div>
    </div>
</body>
</html>
