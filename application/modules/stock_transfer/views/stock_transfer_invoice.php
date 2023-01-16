<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title> Stock Adjustment Receipt </title>

<style type="text/css" media="all">

	body{ font-size:0.75em; font-family:Arial, Helvetica, sans-serif; margin:0; padding:0;}
	#container{ width:21cm; height:11.6cm; border:0pt solid #000;}
	.clear{ clear:both;}
	#tablebox{ height:6.45cm; width:20cm; border:0pt solid red; float:left; margin:0.1cm 0 0 0.4cm;}
		
	#logobox{ width:5.5cm; height:1cm; border:0pt solid blue; margin:0.8cm 0 0 0.5cm; float:left;}
	#venbox{ width:7.5cm; height:2.0cm; border:0pt solid green; margin:0.0cm 0cm 0.2cm 0.5cm; float:left;}
    #venbox2{ width:7.5cm; height:2.0cm; border:0pt solid green; margin:0.0cm 0cm 0.2cm 0.5cm; float:right;}
	#title{ text-align:center; font-size:17pt;}
	h4{ font-size:14pt; margin:0;}
</style>

    <link rel="stylesheet" href="<?php echo base_url().'js-old/jxgrid/' ?>css/jqx.base.css" type="text/css" />
    
	<script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxcore.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxdata.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxbuttons.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxcheckbox.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxscrollbar.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxlistbox.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxdropdownlist.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxmenu.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxgrid.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxgrid.sort.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxgrid.filter.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxgrid.columnsresize.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxgrid.columnsreorder.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxgrid.selection.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxgrid.pager.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxgrid.aggregates.js"></script>
    <script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxdata.export.js"></script>
	<script type="text/javascript" src="<?php echo base_url().'js-old/jxgrid/' ?>js/jqxgrid.export.js"></script>
    
    <script type="text/javascript">
	
        $(document).ready(function () {
          
			var rows = $("#table tbody tr");
                // select columns.
                var columns = $("#table thead th");
                var data = [];
                for (var i = 0; i < rows.length; i++) {
                    var row = rows[i];
                    var datarow = {};
                    for (var j = 0; j < columns.length; j++) {
                        // get column's title.
                        var columnName = $.trim($(columns[j]).text());
                        // select cell.
                        var cell = $(row).find('td:eq(' + j + ')');
                        datarow[columnName] = $.trim(cell.text());
                    }
                    data[data.length] = datarow;
                }
                var source = {
                    localdata: data,
                    datatype: "array",
                    datafields:
                    [
                        { name: "No", type: "string" },
						{ name: "Product", type: "string" },
						{ name: "Qty", type: "number" },
						{ name: "Amount", type: "number" },
						{ name: "Balance", type: "number" }
                    ]
                };
			
            var dataAdapter = new $.jqx.dataAdapter(source);
            $("#jqxgrid").jqxGrid(
            {
                width: '100%',
				source: dataAdapter,
				sortable: true,
				filterable: true,
				pageable: true,
				altrows: true,
				enabletooltips: true,
				filtermode: 'excel',
				autoheight: true,
				columnsresize: true,
				columnsreorder: true,
				showstatusbar: true,
				statusbarheight: 30,
				showaggregates: true,
				autoshowfiltericon: false,
                columns: [
                  { text: 'No', dataField: 'No', width: 50 },
				  { text: 'Product', dataField: 'Product', cellsalign: 'center' },
  				  { text: 'Qty', dataField: 'Qty', width : 60 },
 				  { text: 'Amount', datafield: 'Amount', width: 150, cellsalign: 'right', cellsformat: 'number' },
				  { text: 'Balance', dataField: 'Balance', cellsalign: 'right', cellsformat: 'number', aggregates: ['sum']}
				  
                ]
            });
            
            $("#bexport").click(function() {
				
				var type = $("#crtype").val();	
				if (type == 0){ $("#jqxgrid").jqxGrid('exportdata', 'html', 'Stock-Adj'); }
				else if (type == 1){ $("#jqxgrid").jqxGrid('exportdata', 'xls', 'Stock-Adj'); }
				else if (type == 2){ $("#jqxgrid").jqxGrid('exportdata', 'pdf', 'Stock-Adj'); }
				else if (type == 3){ $("#jqxgrid").jqxGrid('exportdata', 'csv', 'Stock-Adj'); }
			});
			
			$('#jqxgrid').jqxGrid({ pagesizeoptions: ['50', '150', '300']}); 
			
			$("#table").hide();
			
		// end jquery	
        });
    </script>


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
	
    <center>
	   <div style="border:0px solid green; width:500px;">	
	       <h4> <?php echo isset($company) ? $company : ''; ?> </h4>
           <p style="margin:5px; padding:0;"> <?php echo $address; ?> <br> Telp. <?php echo $phone1.' - '.$phone2; ?> <br>
               Website : <?php echo $website; ?> &nbsp; &nbsp; Email : <?php echo $email; ?> </p>
	   </div>
	</center> <hr>
	
    <p style="padding:0; font-weight:bold; font-size:1.3em; text-align:center;"> STOCK TRANSFER ADJUSTMENT </p>
    
    <div id="venbox">
	<table width="100%" style="font-size:1em; margin:0; text-align:left; font-weight:bold;">
	  <tr> <td> No </td> <td>:</td> <td> TRAJ-00<?php echo isset($no) ? $no : ''; ?> </td> </tr>
	  <tr> <td> Date </td> <td>:</td> <td> <?php echo isset($podate) ? tglin($podate) : ''; ?> </td> </tr>
      <tr> <td> From </td> <td>:</td> <td> <?php echo isset($from) ? $from : ''; ?> </td> </tr>
      <tr> <td> To </td> <td>:</td> <td> <?php echo isset($to) ? $to : ''; ?> </td> </tr>
	</table>
	</div>
    
    <div id="venbox2">
	<table width="100%" style="font-size:1em; margin:0; text-align:left; font-weight:bold;">
	  <tr> <td> User </td> <td>:</td> <td> <?php echo isset($user) ? $user : ''; ?> </td> </tr>
      <tr> <td> Log </td> <td>:</td> <td> <?php echo isset($log) ? $log : ''; ?> </td> </tr>
	</table>
	</div>
	
	<div id="tablebox">
    
    <div id='jqxWidget'>
        <div style='margin-top: 10px;' id="jqxgrid"> </div>

        <table style="float:right; margin:5px;">
        <tr>
        <td> <input type="button" id="bexport" value="Export"> - </td>
        <td> 
        <select id="crtype"> <option value="0"> HTML </option> <option value="1"> XLS </option>  <option value="2"> PDF </option> 
        <option value="3"> CSV </option> 
        </select>
        </td>
        </tr>
        </table>
    </div>
	
		<table id="table">
        <thead>
	    <tr>  <th> No </th> <th> Product </th> <th> Qty </th> <th> Amount </th> <th> Balance </th> </tr>	
        </thead>

        <tbody>
        
        <?php
		
		function product($val,$type='sku')
		{
			$pro = new Product_lib();
		    if ($type == 'name'){ return $pro->get_name($val); }
			elseif($type == 'unit'){ return $pro->get_unit($val); }	
            elseif($type == 'sku'){ return $pro->get_sku($val); }	
		}
		
		if ($items)
		{
			$i=1;
			foreach ($items as $res)
			{
				echo "	
				 <tr> 
					<td> ".$i." </td>
					<td class=\"left\"> ".product($res->product_id,'sku').' - '.product($res->product_id,'name')." </td> 
					<td> ".$res->qty." </td> 
					<td class=\"right\"> ".$res->price." </td> 
					<td class=\"right\"> ".intval($res->qty*$res->price)." </td> 
				 </tr>
				
				"; $i++;
			}
		}
		
        ?>
        
        </tbody>

		</table>
	</div>  <div class="clear"></div>
	
	
</div>

</body>
</html>
