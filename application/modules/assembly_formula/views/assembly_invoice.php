
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title> Assembly Receipt </title>

<style type="text/css" media="all">

	body{ font-size:0.75em; font-family:Arial, Helvetica, sans-serif; margin:0; padding:0;}
	#container{ width:21cm; height:11.6cm; border:0pt solid #000;}
	.clear{ clear:both;}
	#tablebox{  width:20cm; border:0pt solid red; float:left; margin:0.1cm 0 0 0.4cm;}
		
	#logobox{ width:5.5cm; height:1cm; border:0pt solid blue; margin:0.8cm 0 0 0.5cm; float:left;}
	#venbox{ width:7.5cm; height:3.9cm; border:0pt solid green; margin:0.0cm 0cm 0.2cm 0.5cm; float:left;}
    #venbox2{ width:7.5cm; height:3.9cm; border:0pt solid green; margin:0.0cm 0cm 0.2cm 0.5cm; float:right;}
	#title{ text-align:center; font-size:17pt;}
	h4{ font-size:14pt; margin:0;}
    
    #logo { margin:0 10px 0 5px;}
	#logotext{ font-size:1em; text-align:center; margin:5px; }
	p { margin:0; padding:0; font-size:1.05em;}
	#pono{ font-size:1.3em; padding:0; margin:0 5px 10px 0; text-align:left;}
	
	table.product
	{ border-collapse:collapse; width:100%; margin-bottom:10px; }
	
	table.product,table.product th
	{	border: 1px solid black; font-size:1.05em; font-weight:bold; padding:3px 0 3px 0; }
	
	table.product,table.product td
	{	border: 1px solid black; font-size:1.05em; font-weight:normal; padding:3px 0 3px 0; text-align:center; }
	
	table.product td.left { text-align:left; padding:3px 5px 3px 10px; }
	table.product td.right { text-align:right; padding:3px 10px 3px 5px; }
	
	#container{ width:20.5cm; font-family:Arial, Helvetica, sans-serif; font-size:12px; border:0px solid red;  }
    
</style>

</head>

<script type="text/javascript">
    
    function closeWindow() {
        setTimeout(function() {
        window.close();
        }, 300000);
    }
    
</script>     
    
<body bgcolor="#FFFFFF"; onload="closeWindow();">

<div id="container">
	<br>
    <p style="padding:0; font-weight:bold; font-size:1.3em; text-align:center;"> A-FORMULA VOUCHER </p> <br>
    
    <div id="venbox">
	<table width="100%" style="font-size:1em; margin:0; text-align:left; font-weight:bold;">
	  <tr> <td> No </td> <td>:</td> <td> ASM-0<?php echo isset($no) ? $no : ''; ?> </td> </tr>
	  <tr> <td> Date </td> <td>:</td> <td> <?php echo isset($podate) ? tglin($podate) : ''; ?> </td> </tr>
      <tr> <td> Docno </td> <td>:</td> <td> <?php echo isset($docno) ? $docno : ''; ?> </td> </tr>
      <tr> <td> Curency </td> <td>:</td> <td> <?php echo isset($currency) ? $currency : ''; ?> </td> </tr>
      <tr> <td> Branch </td> <td>:</td> <td> <?php echo isset($branch) ? $branch : ''; ?> </td> </tr>
      <tr> <td> Log </td> <td>:</td> <td> <?php echo isset($log) ? $log : ''; ?> </td> </tr>
      <tr> <td> Account </td> <td>:</td> <td> <?php echo isset($account) ? $account : ''; ?> </td> </tr>
      <tr> <td> Tax </td> <td>:</td> <td> <?php echo isset($tax) ? $tax : ''; ?> </td> </tr>
	</table>
	</div>
    
    <div id="venbox2">
	<table width="100%" style="font-size:1em; margin:0; text-align:left; font-weight:bold;">
	  <tr> <td> Project </td> <td>:</td> <td> <?php echo isset($project) ? $project : ''; ?> </td> </tr>
      <tr> <td> Product </td> <td>:</td> <td> <?php echo isset($product) ? $product : ''; ?> </td> </tr>
      <tr> <td> Qty </td> <td>:</td> <td> <?php echo isset($qty) ? $qty : ''; ?> </td> </tr>
      <tr> <td> Unitprice </td> <td>:</td> <td> <?php echo isset($unitprice) ? $unitprice : ''; ?> </td> </tr>
      <tr> <td> Cost </td> <td>:</td> <td> <?php echo isset($cost) ? $cost : ''; ?> </td> </tr>
      <tr> <td> Taxamount </td> <td>:</td> <td> <?php echo isset($taxamount) ? $taxamount : ''; ?> </td> </tr>
      <tr> <td> Amount </td> <td>:</td> <td> <?php echo isset($amount) ? $amount : ''; ?> </td> </tr>
      <tr> <td> Notes </td> <td>:</td> <td> <?php echo isset($notes) ? $notes : ''; ?> </td> </tr>
	</table>
	</div>
    
	<div class="clear"></div>
	<div id="tablebox">

    <fieldset> <legend> Material </legend>
    <table class="product">

<tr> <th> No </th> <th> Product </th> <th> Qty </th> <th> Price </th> <th> Amount </th> </tr>	
		 
		 <?php
		 	
		function product($val,$type='name')
		{
			$pro = new Product_lib();
		    if ($type == 'name'){ return $pro->get_sku($val).' : '.$pro->get_name($val); }
			elseif($type == 'unit'){ return $pro->get_unit($val); }	
		}
		
		if ($items)
		{
			$i=1;
			foreach ($items as $res)
			{
				echo "	
				 <tr> 
					<td> ".$i." </td>
                    <td class=\"left\"> ".product($res->product_id,'name')." </td> 
					<td> ".$res->qty.' '.product($res->product_id,'unit')." </td> 
                    <td class=\"right\"> ".idr_format(intval($res->price/$res->qty))." </td> 
					<td class=\"right\"> ".idr_format($res->price)." </td> 
				 </tr>
				
				"; $i++;
			}
		}
			
		 ?>
		 
		 <tr> <td colspan="4"></td>  <td class="right"> <b> <?php echo $unitprice; ?> </b> </td> </tr>
			
		</table>    
        </fieldset> <br>
	    
<!-- cost table -->
<fieldset> <legend> Assembly Cost </legend>
    <table class="product">

<tr> <th> No </th> <th> Notes </th> <th> Amount </th> </tr>	
		 
		 <?php
		 	
		
		if ($costitems)
		{
			$i=1;
			foreach ($costitems as $res)
			{
				echo "	
				 <tr> 
					<td> ".$i." </td>
					<td class=\"left\"> ".$res->notes." </td> 
					<td class=\"right\"> ".idr_format($res->amount)." </td> 
				 </tr>
				
				"; $i++;
			}
		}
			
		 ?>
		 
		 <tr> <td colspan="2"></td>  <td class="right"> <b> <?php echo $cost; ?> </b> </td> </tr>
			
		</table>    
        </fieldset>    
        
		<div style="width:620px; border:0px solid #000; float:right; margin:10px 0px 0 0;">
		<style>
			.sig{ font-size:11px; width:100%; float:right; text-align:center;}
			.sig td{ width:155px;}
		</style>
			<table border="0" class="sig">
				<tr> <td> Approved By : </td> <td> Review By : </td> <td> Prepared By : </td> <td> Received By : </td> </tr>
			</table> <br> <br> <br> <br> <br> 
			
			<table border="0" class="sig">
				<tr> <td> Manager <br> (<?php echo $manager; ?>) </td> <td> Accounting <br> (<?php echo $accounting; ?>) </td> <td> Kasir <br> (<?php echo $user; ?>) </td> <td> (________________) </td> </tr>
			</table>
		</div>
		
		<div style="clear:both; ">
	
    </div>
    
    </div></div>

</body>
</html>
