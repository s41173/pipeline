<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="<?php echo base_url().'images/fav_icon.png';?>" >
<title> <?php echo isset($title) ? $title : ''; ?>  </title>
<style media="all">
	table{ font-family:"Tahoma", Times, serif; font-size:11px;}
	h4{ font-family:"Tahoma", Times, serif; font-size:14px; font-weight:600;}
	.clear{clear:both;}
	table th{ background-color:#EFEFEF; padding:4px 0px 4px 0px; border-top:1px solid #000000; border-bottom:1px solid #000000;}
    p{ font-family:"Tahoma", Times, serif; font-size:12px; margin:0; padding:0;}
	legend{font-family:"Tahoma", Times, serif; font-size:13px; margin:0; padding:0; font-weight:600;}
	.tablesum{ font-size:13px;}
	.strongs{ font-weight:normal; font-size:12px; border-top:1px dotted #000000; }
	.poder{ border-bottom:0px solid #000000; color:#0000FF;}
    .img_product{ height: 50px; align-content: center;}
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
                        { name: "Docno", type: "string" },
						{ name: "Type", type: "string" },
						{ name: "Dates", type: "string" },
						{ name: "Consignee", type: "string" },
						{ name: "T.Source", type: "string" },
                        { name: "Vessel", type: "number" },
                        { name: "Shipper", type: "string" },
                        { name: "Notes", type: "string" },
                        { name: "Fuel", type: "string" },
                        { name: "Oil Boom.", type: "string" },
                        { name: "Laycan - Until", type: "string" },
                        { name: "Heating", type: "string" },
                        { name: "Pumpping", type: "string" },
                        { name: "S.Line Cond", type: "string" },
                        { name: "Bef.Load", type: "string" },
                        { name: "Cleaning Sys", type: "string" },
                        { name: "After Load", type: "string" },
                        { name: "Ship Name", type: "string" },
                        { name: "Ship Rep", type: "string" },
                        { name: "Ship Comp", type: "string" },
                        { name: "Buyer Name", type: "string" },
                        { name: "Buyer Rep", type: "string" },
                        { name: "Buyer Comp", type: "string" },
                        { name: "Posted", type: "string" },
                        { name: "Executed", type: "string" }
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
				  { text: 'Docno', dataField: 'Docno', width : 170 },
				  { text: 'Type', dataField: 'Type', width : 150 },
  				  { text: 'Dates', dataField: 'Dates', width : 150 },
				  { text: 'Consignee', dataField: 'Consignee', width : 200 },
  				  { text: 'T.Source', dataField: 'T.Source', width : 100 },
                  { text: 'Vessel', dataField: 'Vessel', width : 100 },
                  { text: 'Shipper', dataField: 'Shipper', width : 100 },
                  { text: 'Notes', dataField: 'Notes', width : 90 },
                  { text: 'Fuel', dataField: 'Fuel', width : 90 },
                  { text: 'Oil Boom', dataField: 'Oil Boom', width : 160 },
                  { text: 'Laycan - Until', dataField: 'Laycan - Until', width : 320 },
                  { text: 'Heating', dataField: 'Heating', width : 320 },
                  { text: 'Pumpping', dataField: 'Pumpping', width : 320 },
                  { text: 'S.Line Cond', dataField: 'S.Line Cond', width : 140 },
                  { text: 'Bef.Load', dataField: 'Bef.Load', width : 140 },
                  { text: 'Cleaning Sys', dataField: 'Cleaning Sys', width : 140 },
                  { text: 'After Load', dataField: 'After Load', width : 140 },
                  { text: 'Ship Name', dataField: 'Ship Name', width : 160 },
                  { text: 'Ship Rep', dataField: 'Ship Rep', width : 160 },
                  { text: 'Ship Comp', dataField: 'Ship Comp', width : 160 },
                  { text: 'Buyer Name', dataField: 'Buyer Name', width : 160 },
                  { text: 'Buyer Rep', dataField: 'Buyer Rep', width : 160 },
                  { text: 'Buyer Comp', dataField: 'Buyer Comp', width : 160 },
                  { text: 'Posted', dataField: 'Posted', width : 70 },
                  { text: 'Executed', dataField: 'Executed', width : 80 }
                ]
            });
			
			$('#jqxgrid').jqxGrid({ pagesizeoptions: ['1000', '2000', '3000', '5000', '10000', '15000']}); 
			
			$("#bexport").click(function() {
				
				var type = $("#crtype").val();	
				if (type == 0){ $("#jqxgrid").jqxGrid('exportdata', 'html', 'Shore-C-Summary'); }
				else if (type == 1){ $("#jqxgrid").jqxGrid('exportdata', 'xls', 'Shore-C-Summary'); }
				else if (type == 2){ $("#jqxgrid").jqxGrid('exportdata', 'pdf', 'Shore-C-Summary'); }
				else if (type == 3){ $("#jqxgrid").jqxGrid('exportdata', 'csv', 'Shore-C-Summary'); }
			});
			
			$('#jqxgrid').on('celldoubleclick', function (event) {
     	  		var col = args.datafield;
				var value = args.value;
				var res;
			
				if (col == 'Docno')
				{ openwindow(value); }
 			});
			
			function openwindow(val)
			{
				var site = "<?php echo site_url('shorec/invoice/0/');?>";
				window.open(site+"/"+val, "", "width=800, height=600"); 
				//alert(site+"/"+val);
			}
			
			$("#table").hide();
			
		// end jquery	
        });
    </script>
</head>

<body>

<div style="width:100%; border:0px solid blue; font-family:Arial, Helvetica, sans-serif; font-size:12px;">
	
	<div style="border:0px solid red; float:left;">
		<table border="0">
			<tr> <td> Trans Type </td> <td> : </td> <td> <?php echo $transtype; ?> </td> </tr>
            <tr> <td> Consignee </td> <td> : </td> <td> <?php echo $cust; ?> </td> </tr>
            <tr> <td> Period Type </td> <td> : </td> <td> <?php echo $type; ?> </td> </tr>
            <tr> <td> Period </td> <td> : </td> <td> <?php echo $start.'-'.$end; ?> </td> </tr>
			<tr> <td> Run Date </td> <td> : </td> <td> <?php echo $rundate; ?> </td> </tr>
			<tr> <td> Log </td> <td> : </td> <td> <?php echo $log; ?> </td> </tr>
		</table>
	</div>

	<center>
	   <div style="border:0px solid green; width:230px;">	
	       <h4> <?php echo isset($company) ? $company : ''; ?> <br> Shore Calculation - Additional Report </h4>
	   </div>
	</center>
	
	<div class="clear"></div>
	
	<div style="width:100%; border:0px solid brown; margin-top:20px; border-bottom:1px dotted #000000; ">
	
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
    
		<table id="table" border="0" width="100%">
   <thead>
   <tr>
   <th> No </th> <th> Docno </th> <th> Type </th> <th> Dates </th> <th> Consignee </th>
   <th> T.Source </th> <th> Vessel </th> <th> Shipper </th> <th> Notes </th>
   <th> Fuel </th> <th> Oil Boom </th> <th> Laycan - Until </th> <th> Heating </th> <th> Pumpping </th> 
   <th> S.Line Cond </th> <th> Bef.Load </th> <th> Cleaning Sys </th> <th> After Load </th> <th> Ship Name </th>
   <th> Ship Rep </th> <th> Ship Comp </th> <th> Buyer Name </th> <th> Buyer Rep </th> <th> Buyer Comp </th>
   <th> Posted </th> <th> Executed </th>
   </tr>
   </thead>
		  
          <tbody> 
		  <?php 
		      
              function get_type($val=0){
                switch ($val) {
                    case 0: return "Intertank"; break;
                    case 1: return "Ship Outward"; break;
                    case 2: return "Ship Inward"; break;
                    case 3: return "Outward-3-Party"; break;
                }
              }
              
              function tank($val){
                  $tank = new Tank_lib();
                  return $tank->get_details($val,'sku');
              }
              
              function cust($val){
                  $tank = new Customer_lib();
                  return $tank->get_name($val);
              }
              
              function pstatus($val){ if ($val == 0){ return 'N'; }else{ return 'Y'; } }
			  		  
		      $i=1; 
			  if ($reports)
			  {
				foreach ($reports as $res)
				{	
				   echo " 
				   <tr> 
				       <td class=\"strongs\">".$i."</td> 
					   <td class=\"strongs\">".$res->docno."</td>
                       <td class=\"strongs\">".get_type($res->type)."</td>
                       <td class=\"strongs\">".$res->dates."</td>
                       <td class=\"strongs\">".cust($res->cust_id)."</td>
                       <td class=\"strongs\">".tank($res->tank_source)."</td>
                       <td class=\"strongs\">".tank($res->vessel)."</td>
                       <td class=\"strongs\">".$res->shipper."</td>
                       <td class=\"strongs\">".$res->notes."</td>
                       <td class=\"strongs\">".pstatus($res->fuel)."</td>
                       <td class=\"strongs\">".pstatus($res->oil_boom)."</td>
<td class=\"strongs\">".tglincompletetime($res->laycan,'s').' - '.tglincompletetime($res->until,'s')."</td>
<td class=\"strongs\">".tglincompletetime($res->heating,'s').' - '.tglincompletetime($res->heating_until,'s')."</td>
<td class=\"strongs\">".tglincompletetime($res->start_pump,'s').' - '.tglincompletetime($res->end_pump,'s')."</td>
                       <td class=\"strongs\">".$res->shore_line_cond."</td>
                       <td class=\"strongs\">".$res->before_load."</td>
                       <td class=\"strongs\">".$res->cleaning_sys."</td>
                       <td class=\"strongs\">".$res->after_load."</td>
                       <td class=\"strongs\">".$res->ship_name."</td>
                       <td class=\"strongs\">".$res->ship_rep."</td>
                       <td class=\"strongs\">".$res->ship_company."</td>
                       <td class=\"strongs\">".$res->buyer_name."</td>
                       <td class=\"strongs\">".$res->buyer_rep."</td>
                       <td class=\"strongs\">".$res->buyer_company."</td>
                       <td class=\"strongs\">".pstatus($res->approved)."</td>
                       <td class=\"strongs\">".pstatus($res->execution)."</td>
				   </tr>";
				   $i++;
				}
			 }  
		  ?>
		</tbody>      
		</table>
        
        </div>
        
        <a style="float:left; margin:10px;" title="Back" href="<?php echo site_url('shorec'); ?>"> 
          <img src="<?php echo base_url().'images/back.png'; ?>"> 
        </a>
            
</div>

</body>
</html>
