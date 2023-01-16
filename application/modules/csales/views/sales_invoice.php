<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title> Sales Order - SO-00<?php echo isset($pono) ? $pono : ''; ?></title>
<style media="all">

	#container{ width:675px; font-family:Arial, Helvetica, sans-serif; font-size:12px; border-bottom:5px solid #003663; margin:0; }
	#logobox{ float:left; width:320px; height:92px; border:0px solid #FF0000; margin:0 0 0 5px; }
	#content{ float:left; width:550px; height:550px; border:0px solid blue; margin:5px 0 5px 25px; }
	#addressbox{ width:300px; height:90px; border:0px solid #000; float:right; }
	#content table{ font-family:"Times New Roman", Times, serif; font-style:italic; font-size:15px; margin:0px 0 0 0;}
	.clear{ clear:both;}
	h4{ font-family:"Times New Roman", Times, serif; font-size:16px; margin:15px 0 0 0; padding:0 0 0 5px;}
	
	.tab1{ margin:0; border:0px solid red; }
	.tab1 p{font-style:normal; padding:0px; font-size:11pt;}
	.tab2 p{font-style:normal; padding:0px; margin:0; font-size:11pt;}
	#logo{ margin:0; padding:0;}
	#logotext{ text-align:right; font-size:8.5pt; padding:2px; margin:5px 0 0 0;}
	.border{ width:675px; height:6px; border-bottom:5px solid #003663; margin:0; }
	
</style>
</head>

<body onLoad="">

<div id="container">
	
	<div id="logobox"> <img id="logo" align="middle" width="250" height="102" src="<?php echo $logo; ?>"> </div>
    <div id="addressbox">
 		<p id="logotext"> 
        <b> Showroom : </b> <br>
		  <?php echo $paddress; ?> <br> Kotamadya Medan - <?php echo $p_zip; ?> &nbsp; Telp. <?php echo $p_phone1.' / '.$p_phone2 ?> <br>
		  E-Mail : <?php echo $p_email; ?> <br> Website : <?php echo $p_sitename; ?>
		</p>
    </div>
    
    <div class="clear"></div> <br>
    <div class="border"></div>
	
	<div id="content">
		
		<table class="tab1" border="0">
            <tr> <td> <b> No. </b> </td> <td><p>:</p></td> <td> <p> <?php echo $pono; ?> </p> </td>  </tr>
			<tr> <td> <b> Customer </b> </td> <td><p>:</p></td> <td> <p> <?php echo $customer; ?> </p> </td>  </tr>
            <tr> <td> <b> Address / Phone </b> </td> <td><p>:</p></td> <td> <p> <?php echo $address.'<br>'.$phone; ?> </p> </td>  </tr>
			<tr> <td> <b> Amount </b> </td> <td><p>:</p></td> <td> <p> <?php echo $terbilang; ?> </p> </td>  </tr>
			<tr> <td style="width:125px;"> <b> Notes </b> </td> <td><p>:</p></td> 
			<td> <p> <?php echo $notes; ?> </p> </td>  </tr>
		</table>
		<div class="clear"></div>
		
		<h4> Transaction Details : </h4>
		
		<table class="tab2" style="margin:5px 0px 0 3px;" width="500px">
        
    <tr> <td> <p> Sub Total </p> </td> <td><p>:</p></td> <td><p> </p></td> <td><p> = </p></td>
    <td align="right"><p> <?php echo number_format($bruto,0,",","."); ?> ,- </p></td> </tr>        
            
	<tr> <td> <p> Discount </p> </td> <td><p>:</p></td> <td><p> <?php echo $discountpercent; ?> % </p></td> <td><p>= <?php echo $symbol; ?></p></td> <td align="right"><p> 
	<?php echo number_format($discount,0,",","."); ?> ,- </p></td> </tr>
            
	<tr> <td> <p> Rest Balance </p> </td> <td><p>:</p></td> <td><p> <?php echo intval(100-$discountpercent); ?> % </p></td> <td><p>= <?php echo $symbol; ?></p></td> 
	<td align="right"><p> <?php echo number_format($netto,0,",","."); ?> ,- </p></td> </tr>
	<tr> <td> <p> VAT (Tax) </p> </td> <td><p>:</p></td> <td><p> <?php echo $tax; ?> % </p></td> <td><p>= <?php echo $symbol; ?></p></td> 
	<td align="right"><p> <?php echo number_format($tax_val,0,",","."); ?> ,- </p></td> </tr>
	<tr> <td> <p> Cost </p> </td> <td><p>:</p></td> <td><p> </p></td> <td><p>= <?php echo $symbol; ?></p></td> 
	<td align="right"><p> <?php echo number_format($cost,0,",","."); ?> ,- </p></td> </tr>
	<tr> <td>  </td> <td></td> <td align="right"><p> Total </p></td> <td><p>= <?php echo $symbol; ?></p></td> 
	           <td align="right"><p> <b> <?php echo number_format($total,0,",","."); ?> ,- </b> </p></td> </tr>
		</table>
		
		<p style="float:right; font-family:'Times New Roman', Times, serif; font-size:15px; margin:5px 50px 0 0;"> <i> Medan, &nbsp; </i> <?php echo $podate; ?> </p> 
		<div class="clear"></div>
		
		<div style="border-top:2px solid #000; border-bottom:2px solid #000; width:150px; height:40px; margin:0px 0 0 8px;">
			<p style="font-family:'Times New Roman', Times, serif; font-size:16px; margin:0; padding:9px 0 0 20px;">
			<b> <?php echo $symbol; ?> <?php echo number_format($total,0,",","."); ?> ,- </b> </p>
		</div>  <div class="clear"></div>
		
		<div style="width:325px; height:90px; border:0px solid #000; margin:20px 0 0 6px; float:left;">
        <p style="font-style:italic"> Log : <?php echo $this->session->userdata('log'); ?> </p>
        <p style="font-size:8pt; font-weight:bold;"> Pembayaran Di Lakukan Melalui Transfer : <br> 
            
<?php
    
    if ($banklist){
        foreach($banklist as $res){
     echo "Bank ".strtoupper($res->acc_bank)." An: ".strtoupper($res->acc_name)." / ".$res->acc_no.'<br>';
        }
    }

?>
        </p>
        </div>
		<div style="width:200px; border:0px solid #000; margin:0px 0 0 20px; float:left;">
			<p style="margin:0; padding:15px 0 0 0;"> (...............................................) <br> &nbsp; &nbsp; &nbsp; <b> <?php echo $p_name; ?> </b> </p>
		</div> 
        
	</div>
    <div class="clear"></div>
	
</div>

</body>
</html>