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
						{ name: "Date", type: "string" },
						{ name: "Code", type: "string" },
						{ name: "Docno", type: "string" },
                        { name: "Currency", type: "string" },
                        { name: "Branch", type: "string" },
                        { name: "Account", type: "string" },
                        { name: "Notes", type: "string" },
                        { name: "Project", type: "string" },
						{ name: "Product", type: "string" },
						{ name: "Qty", type: "number" },
                        { name: "Unitprice", type: "number" },
                        { name: "Cost", type: "number" },
                        { name: "Tax", type: "number" },
						{ name: "Amount", type: "number" },
						{ name: "Posted", type: "string" }
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
				  { text: 'Date', dataField: 'Date', width : 100 },
  				  { text: 'Code', dataField: 'Code', width : 100 },
				  { text: 'Docno', dataField: 'Docno', width : 110 },
                  { text: 'Currency', dataField: 'Currency', width : 100 },
                  { text: 'Branch', dataField: 'Branch', width : 100 },
                  { text: 'Account', dataField: 'Account', width : 180 },
                  { text: 'Notes', dataField: 'Notes', width : 200 },
                  { text: 'Project', dataField: 'Project', width : 150 },
				  { text: 'Product', dataField: 'Product', width : 180 },
{ text: 'Qty', dataField: 'Qty', width : 100, cellsalign: 'center', cellsformat: 'number', aggregates: ['sum'] },
{ text: 'Unitprice', dataField: 'Unitprice', width: 150, cellsalign: 'right', cellsformat: 'number', aggregates: ['sum'] },
{ text: 'Cost', dataField: 'Cost', width: 150, cellsalign: 'right', cellsformat: 'number', aggregates: ['sum'] },
{ text: 'Tax', dataField: 'Tax', width: 150, cellsalign: 'right', cellsformat: 'number', aggregates: ['sum'] },
{ text: 'Amount', dataField: 'Amount', width: 150, cellsalign: 'right', cellsformat: 'number', aggregates: ['sum'] },
{ text: 'Posted', dataField: 'Posted', width : 90 }
				  
                ]
            });
			
			$("#bexport").click(function() {
				
				var type = $("#crtype").val();	
				if (type == 0){ $("#jqxgrid").jqxGrid('exportdata', 'html', 'Assembly'); }
				else if (type == 1){ $("#jqxgrid").jqxGrid('exportdata', 'xls', 'Assembly'); }
				else if (type == 2){ $("#jqxgrid").jqxGrid('exportdata', 'pdf', 'Assembly'); }
				else if (type == 3){ $("#jqxgrid").jqxGrid('exportdata', 'csv', 'Assembly'); }
			});
			
			$('#jqxgrid').jqxGrid({ pagesizeoptions: ['100', '200', '300', '500', '1000', '2000', '3000', '5000']}); 
			
			$('#jqxgrid').on('celldoubleclick', function (event) {
     	  		var col = args.datafield;
				var value = args.value;
				var res;
			
				if (col == 'Code')
				{ 			
				   res = value.split("ASM-0");
				   openwindow(res[1]);
				}
 			});
			
			function openwindow(val)
			{
				var site = "<?php echo site_url('assembly/invoice/');?>";
				window.open(site+"/"+val, "", "width=800, height=600"); 
			}
            
			$("#table").hide();
			
        });
    </script>

</head>

<body>

<div style="width:100%; border:0px solid blue; font-family:Arial, Helvetica, sans-serif; font-size:12px;">
	
	<div style="border:0px solid red; float:left;">
		<table border="0">
			<tr> <td> Period </td> <td> : </td> <td> <?php echo tglin($start); ?> to <?php echo tglin($end); ?> </td> </tr>
			<tr> <td> Run Date </td> <td> : </td> <td> <?php echo $rundate; ?> </td> </tr>
			<tr> <td> Log </td> <td> : </td> <td> <?php echo $log; ?> </td> </tr>
		</table>
	</div>

	<center>
	   <div style="border:0px solid green; width:230px;">	
	      <h4> <?php echo isset($company) ? $company : ''; ?> <br> Assembly - Report Summary </h4>
	   </div>
	</center>
	
	<div class="clear"></div>
	
	<div style="width:100%; border:0px solid brown; margin-top:20px; border-bottom:0px dotted #000000; ">
	
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
        
	</div>

</div>

<table id="table" border="0">
 <thead>
 <tr>
 <th>No</th> <th>Date</th> <th> Code </th> <th> Docno </th> <th> Currency </th> <th> Branch </th> <th> Account </th> 
 <th> Notes </th> <th> Project </th> <th> Product </th> <th> Qty </th> <th> Unitprice </th> <th> Cost </th> <th> Tax </th> <th> Amount </th> <th> Posted </th>
 </tr>
 </thead>
   
  <tbody>
  <?php 	
  
      $i=1; 
      $val = 0;
      if ($reports)
      {
		function product($val,$type='name')
		{
			$pro = new Product_lib();
		    if ($type == 'name'){ return $pro->get_name($val); }
			elseif($type == 'unit'){ return $pro->get_unit($val); }	
            elseif($type == 'sku'){ return $pro->get_sku($val); }	
		}  
          
        function account($val){
            $acc = new Account_lib();
            return $acc->get_code($val).' : '.$acc->get_name($val);
        }
          
        function branch($val){
            $acc = new Branch_lib();
            return $acc->get_name($val);
        }
          
        function posted($val){ if ($val == 0){ return 'N'; }else{ return 'Y'; }}
		  
        foreach ($reports as $res)
        {	
           echo "
           <tr> 
           <td class=\"strongs\" align=\"center\">".$i."</td> 
           <td class=\"strongs\" align=\"center\">".tglin($res->dates)."</td>
           <td class=\"strongs\" align=\"center\">ASM-0".$res->id."</td>
           <td class=\"strongs\" align=\"center\">".strtoupper($res->docno)."</td> 
           <td class=\"strongs\" align=\"center\">".strtoupper($res->currency)."</td> 
           <td class=\"strongs\" align=\"center\">".branch($res->branch_id)."</td> 
           <td class=\"strongs\" align=\"center\">".account($res->account)."</td>
           <td class=\"strongs\" align=\"center\">".$res->notes."</td>
           <td class=\"strongs\" align=\"center\">".$res->project."</td>
<td class=\"strongs\" align=\"left\">".product($res->product,'sku').' - '.product($res->product,'name')."</td>
           <td class=\"strongs\" align=\"left\">".$res->qty."</td>
		   <td class=\"strongs\" align=\"left\">".$res->unitprice."</td> 
           <td class=\"strongs\" align=\"left\">".$res->costs."</td>
           <td class=\"strongs\" align=\"left\">".$res->taxamount."</td>
           <td class=\"strongs\" align=\"left\">".$res->amount."</td>
           <td class=\"strongs\" align=\"left\">".posted($res->approved)."</td>
           </tr>";
           $i++; 
        }
      }  
  ?>
  
  </tbody>
   
</table>
<a style="float:left; margin:10px;" title="Back" href="<?php echo site_url('assembly'); ?>"> 
  <img src="<?php echo base_url().'images/back.png'; ?>"> 
</a>
</body>
</html>
