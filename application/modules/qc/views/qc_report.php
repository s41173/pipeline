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
                        { name: "NO", type: "string" },
                        { name: "TYPE", type: "string" },
                        { name: "CODE", type: "string" },
						{ name: "DATE", type: "string" },
						{ name: "DOCNO", type: "string" },
						{ name: "ORIGIN NO", type: "string" },
						{ name: "SUPPLIER", type: "string" },
                        { name: "GK NO", type: "string" },
                        { name: "FFA", type: "string" },
                        { name: "M", type: "string" },
                        { name: "I", type: "string" },
                        { name: "DESCRIPTION", type: "string" },
                        { name: "QC STATUS", type: "string" },
                        { name: "VALIDATION", type: "string" },
                        { name: "POSTED", type: "string" }
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
                  { text: 'NO', dataField: 'NO', width: 50 },
                  { text: 'TYPE', dataField: 'TYPE', width: 80 },
                  { text: 'CODE', dataField: 'CODE', width: 150 },
				  { text: 'DATE', dataField: 'DATE', width : 150 },
                  { text: 'DOCNO', dataField: 'DOCNO', width : 230 },
                  { text: 'ORIGIN NO', dataField: 'ORIGIN NO', width : 270 },
                  { text: 'SUPPLIER', dataField: 'SUPPLIER', width : 150 },
				  { text: 'GK NO', dataField: 'GK NO', width : 130 },
				  { text: 'FFA', dataField: 'FFA', width : 80 },
                  { text: 'M', dataField: 'M', width : 80 },
                  { text: 'I', dataField: 'I', width : 80 },
                  { text: 'DESCRIPTION', dataField: 'DESCRIPTION' },
                  { text: 'QC STATUS', dataField: 'QC STATUS', width : 120 },
                  { text: 'VALIDATION', dataField: 'VALIDATION', width : 110 },
                  { text: 'POSTED', dataField: 'POSTED', width : 80 }
                ]
            });
			
			$('#jqxgrid').jqxGrid({ pagesizeoptions: ['1000', '2000', '3000', '5000', '10000', '15000']}); 
			
			$("#bexport").click(function() {
				
				var type = $("#crtype").val();	
				if (type == 0){ $("#jqxgrid").jqxGrid('exportdata', 'html', 'QC-Summary'); }
				else if (type == 1){ $("#jqxgrid").jqxGrid('exportdata', 'xls', 'QC-Summary'); }
				else if (type == 2){ $("#jqxgrid").jqxGrid('exportdata', 'pdf', 'QC-Summary'); }
				else if (type == 3){ $("#jqxgrid").jqxGrid('exportdata', 'csv', 'QC-Summary'); }
			});
			
			$('#jqxgrid').on('celldoubleclick', function (event) {
     	  		var col = args.datafield;
				var value = args.value;
				var res;
			
				if (col == 'Code')
				{ 			
				   res = value.split("CO-0");
				   openwindow(res[1]);
				}
 			});
			
			function openwindow(val)
			{
				var site = "<?php echo site_url('contract/invoice/');?>";
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
			<tr> <td> Run Date </td> <td> : </td> <td> <?php echo $rundate; ?> </td> </tr>
			<tr> <td> Log </td> <td> : </td> <td> <?php echo $log; ?> </td> </tr>
		</table>
	</div>

	<center>
	   <div style="border:0px solid green; width:230px;">	
	       <h4> <?php echo isset($company) ? $company : ''; ?> <br> QC-Report </h4>
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
<th> NO </th> <th> TYPE </th> <th> CODE </th> <th> DATE </th> <th> DOCNO </th> <th> ORIGIN NO </th>
<th> SUPPLIER </th> <th> GK NO </th>
<th> FFA </th> <th> M </th> <th> I </th> <th> DESCRIPTION </th> <th> QC STATUS </th>
<th> VALIDATION </th> <th> POSTED </th>
		   </tr>
           </thead>
		  
          <tbody> 
		  <?php 
              
function postedstatus($val){ if ($val == 0){ return 'N'; }else{ return 'Y'; } }
function qcstatus($val){ if ($val == 0)
{ return 'OK'; }elseif($val == 1){ return 'NOT OK'; }else{ return "REJECT"; } }
			  		  
		      $i=1; 
			  if ($reports)
			  {
				foreach ($reports as $res)
				{	
				   echo " 
				   <tr> 
				       <td class=\"strongs\">".$i."</td> 
                       <td class=\"strongs\">".$res->type."</td> 
                       <td class=\"strongs\">".$res->code."</td> 
					   <td class=\"strongs\">".tglincompletetime($res->dates,1)."</td>
                       <td class=\"strongs\">".$res->docno."</td>
                       <td class=\"strongs\">".$res->origin_no."</td>
                       <td class=\"strongs\">".$res->supplier."</td>
                       <td class=\"strongs\">".$res->gk_no."</td>
                       <td class=\"strongs\">".$res->ffa."</td>
                       <td class=\"strongs\">".$res->moist."</td>
                       <td class=\"strongs\">".$res->imp."</td>
                       <td class=\"strongs\">".$res->description."</td>
                       <td class=\"strongs\">".qcstatus($res->qc_status)."</td>
                       <td class=\"strongs\">".postedstatus($res->validation)."</td>
                       <td class=\"strongs\">".postedstatus($res->approved)."</td>
				   </tr>";
				   $i++;
				}
			 }  
		  ?>
		</tbody>      
		</table>
        
        </div>
        
        <a style="float:left; margin:10px;" title="Back" href="<?php echo site_url('qc'); ?>"> 
          <img src="<?php echo base_url().'images/back.png'; ?>"> 
        </a>
        
    </div> 

</body>
</html>
