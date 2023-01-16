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
	table th{ background-color:#EFEFEF; padding:4px 0px 4px 0px; border-top:1px solid #000000; border-bottom:0px solid #000000;}
    p{ font-family:"Arial", Times, serif; font-size:12px; margin:0; padding:0;}
	legend{font-family:"Arial", Times, serif; font-size:13px; margin:0; padding:0; font-weight:600;}
	.tablesum{ font-size:13px;}
	.strongs{ font-weight:normal; font-size:12px; border-top:0px dotted #000000; }
	.poder{ border-bottom:0px solid #000000; color:#0000FF;}
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
                        { name: "Doc-No", type: "string" },
						{ name: "Tank", type: "string" },
						{ name: "Notes", type: "string" },
                        { name: "Sounding", type: "number" },
                        { name: "Corr", type: "number" },
                        { name: "A.Corr", type: "number" },
                        { name: "Temperature", type: "string" },
                        { name: "Density", type: "string" },
                        { name: "Coeff", type: "string" },
                        { name: "Obv", type: "number" },
                        { name: "Adj", type: "number" },
                        { name: "Vcv", type: "number" },
                        { name: "Netkg", type: "number" },
                        { name: "Ffa (%)", type: "number" },
                        { name: "Moisture (%)", type: "number" },
                        { name: "Dirt (%)", type: "number" },
                        { name: "Log", type: "string" },
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
                  { text: 'Date', dataField: 'Date', width : 130 },
				  { text: 'Doc-No', dataField: 'Doc-No', width : 180 },
                  { text: 'Tank', dataField: 'Tank', width : 120 },
                  { text: 'Notes', dataField: 'Notes', width : 200 },
                  { text: 'Sounding', datafield: 'Sounding', width: 150, cellsalign: 'right', cellsformat: 'F2' },
                  { text: 'Corr', datafield: 'Corr', width: 130, cellsalign: 'right', cellsformat: 'F2' },
                  { text: 'A.Corr', datafield: 'A.Corr', width: 150, cellsalign: 'right', cellsformat: 'F2' },    
{ text: 'Temperature', datafield: 'Temperature', width: 100, cellsalign: 'right', cellsformat: 'F2' },
{ text: 'Density', datafield: 'Density', width: 110, cellsalign: 'right', cellsformat: 'F2' },
{ text: 'Coeff', datafield: 'Coeff', width: 110, cellsalign: 'right', cellsformat: 'F2' },
{ text: 'Obv', datafield: 'Obv', width: 150, cellsalign: 'right', cellsformat: 'F2' },
{ text: 'Adj', datafield: 'Adj', width: 90, cellsalign: 'right', cellsformat: 'F2' },
{ text: 'Vcv', datafield: 'Vcv', width: 90, cellsalign: 'right', cellsformat: 'F2' },
{ text: 'Netkg', datafield: 'Netkg', width: 150, cellsalign: 'right', cellsformat: 'F2' },
{ text: 'Ffa (%)', datafield: 'Ffa (%)', width: 120, cellsalign: 'right', cellsformat: 'F2' },
{ text: 'Moisture (%)', datafield: 'Moisture (%)', width: 120, cellsalign: 'right', cellsformat: 'F2' },
{ text: 'Dirt (%)', datafield: 'Dirt (%)', width: 120, cellsalign: 'right', cellsformat: 'F2' },
{ text: 'Log', dataField: 'Log', width: 120 },
                  { text: 'Posted', dataField: 'Posted', width : 100 },
				  
                ]
            });
			
			$('#jqxgrid').jqxGrid({ pagesizeoptions: ['100', '200', '300', '400', '500', '1000', '2000', '3000']}); 
			
			$("#bexport").click(function() {
				
				var type = $("#crtype").val();	
				if (type == 0){ $("#jqxgrid").jqxGrid('exportdata', 'html', 'Sounding-Summary'); }
				else if (type == 1){ $("#jqxgrid").jqxGrid('exportdata', 'xls', 'Sounding-Summary'); }
				else if (type == 2){ $("#jqxgrid").jqxGrid('exportdata', 'pdf', 'Sounding-Summary'); }
				else if (type == 3){ $("#jqxgrid").jqxGrid('exportdata', 'csv', 'Sounding-Summary'); }
			});
			
//			$('#jqxgrid').on('celldoubleclick', function (event) {
//     	  		var col = args.datafield;
//				var value = args.value;
//				var res;
//			
//				if (col == 'Doc-No')
//				{ 			
////				   res = value.split("-00");
//				   openwindow(value);
//				}
// 			});
			
//			function openwindow(col)
//			{
//				var site = "<?php echo site_url('tankgl/invoice_po/');?>";
//				window.open(site+"/"+col, "", "width=800, height=600"); 
//				//alert(site+"/"+val);
//			}
			
			$("#table").hide();  
			
		// end jquery	
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
	       <h4> <?php echo isset($company) ? $company : ''; ?> <br> Sounding Transaction - Report </h4>
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

<table id="table" border="0" width="100%">
<thead>
<tr>
<th> No </th> <th> Date </th> <th> Doc-No </th> <th> Tank </th> <th> Notes </th> <th> Sounding </th> <th> Corr </th> <th> A.Corr </th> <th> Temperature </th> <th> Density </th> <th> Coeff </th> <th> Obv </th> <th> Adj </th> 
<th> Vcv </th> <th> Netkg </th> <th> Ffa (%) </th> <th> Moisture (%) </th> <th> Dirt (%) </th>
<th> Log </th> <th> Posted </th>
</tr>
</thead>

<tbody>

<?php 

  function approval($val){ if ($val == 0){ return 'N'; }else { return 'Y'; } }
  function tank($val){
      $tank = new Tank_lib();
      return $tank->get_details($val,'sku');
  }

  $i=1; 
  $val = 0;
  if ($journals)
  {
    foreach ($journals as $ap)
    {	
       echo " 
       <tr> 
           <td class=\"strongs\">".$i."</td> 
           <td class=\"strongs\">".tglin($ap->dates)."</td> 
           <td class=\"strongs\">".$ap->docno."</td>
		   <td class=\"strongs\">".tank($ap->tank_id)."</td>
           <td class=\"strongs\">".$ap->notes."</td>
           <td class=\"strongs\">".$ap->sounding."</td>
           <td class=\"strongs\">".$ap->corr."</td>
           <td class=\"strongs\">".$ap->after_corr."</td>
           <td class=\"strongs\">".$ap->temperature."</td>
           <td class=\"strongs\">".$ap->density."</td>
           <td class=\"strongs\">".$ap->coeff."</td>
           <td class=\"strongs\">".$ap->obv."</td>
           <td class=\"strongs\">".$ap->adj."</td>
           <td class=\"strongs\">".$ap->vcv."</td>
           <td class=\"strongs\">".$ap->netkg."</td>
           <td class=\"strongs\">".$ap->ffa."</td>
           <td class=\"strongs\">".$ap->moisture."</td>
           <td class=\"strongs\">".$ap->dirt."</td>
           <td class=\"strongs\">".$ap->log."</td>
           <td class=\"strongs\">".approval($ap->approved)."</td>
       </tr>";
       $i++;
    }
  }  
?>
</tbody>

</table>

<a style="float:left; margin:10px;" title="Back" href="<?php echo site_url('sounding'); ?>"> 
  <img src="<?php echo base_url().'images/back.png'; ?>"> 
</a>
    
</body>
</html>
