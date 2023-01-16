<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="shortcut icon" href="<?php echo base_url().'images/fav_icon.png';?>" >
<title> <?php echo isset($title) ? $title : ''; ?>  </title>
<style media="all">
	table{ font-family:"Arial", Times, serif; font-size:11px;}
	table th{ font-family:arial; font-size:10pt;}
	h4{ font-family:"Arial", Times, serif; font-size:14pt; font-weight:600; margin:0;}
	.clear{clear:both;}
	table th{ background-color:#000; color:#fff; padding:4px 0px 4px 0px; border-top:1px solid #000000; border-bottom:1px solid #000000;}
    p{ font-family:"Arial", Times, serif; font-size:12px; margin:0; padding:0;}
	legend{font-family:"Arial", Times, serif; font-size:13px; margin:0; padding:0; font-weight:600;}
	.tablesum{ font-size:13px;}
	.strongs{ font-weight:normal; font-size:12px; border-top:1px dotted #000000; border-right:1px dotted #000; text-transform: capitalize; }
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
                        { name: "Cust", type: "string" },
                        { name: "Doctype", type: "string" },
						{ name: "Docno", type: "string" },
  					    { name: "Debit", type: "number" },
						{ name: "Credit", type: "number" },
						{ name: "Balance", type: "number" },
						{ name: "Sounding", type: "number" },
                        { name: "Diff", type: "number" },
                        { name: "FFA", type: "number" },
                        { name: "Moisture", type: "number" },
                        { name: "Dirt", type: "number" }
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
				  { text: 'Date', dataField: 'Date', width : 120 },
                  { text: 'Cust', dataField: 'Cust', width : 150 },
                  { text: 'Doctype', dataField: 'Doctype', width : 90 },
  				  { text: 'Docno', dataField: 'Docno', width : 200 },
{ text: 'Debit', datafield: 'Debit', width : 120, cellsalign: 'center', cellsformat: 'number', aggregates: ['sum'] },
{ text: 'Credit', width : 120, datafield: 'Credit', cellsalign: 'center', cellsformat: 'number', aggregates: ['sum'] },
{ text: 'Balance', width : 150, datafield: 'Balance', cellsalign: 'center', cellsformat: 'number' },
{ text: 'Sounding', width : 150, datafield: 'Sounding', cellsalign: 'center', cellsformat: 'number' },
{ text: 'Diff', width : 150, datafield: 'Diff', cellsalign: 'center', cellsformat: 'number' },
{ text: 'FFA', width : 120, datafield: 'FFA', cellsalign: 'center', cellsformat: 'number' },
{ text: 'Moisture', width : 120, datafield: 'Moisture', cellsalign: 'center', cellsformat: 'number' },
{ text: 'Dirt', width : 120, datafield: 'Dirt', cellsalign: 'center', cellsformat: 'number' }
				  
                ]
            });
			
			$('#jqxgrid').jqxGrid({ pagesizeoptions: ['10', '50', '100', '250', '500', '1000', '2000', '3000']}); 
			
			$("#bexport").click(function() {
				
				var type = $("#crtype").val();	
				if (type == 0){ $("#jqxgrid").jqxGrid('exportdata', 'html', 'Stock-Card'); }
				else if (type == 1){ $("#jqxgrid").jqxGrid('exportdata', 'xls', 'Stock-Card'); }
				else if (type == 2){ $("#jqxgrid").jqxGrid('exportdata', 'pdf', 'Stock-Card'); }
				else if (type == 3){ $("#jqxgrid").jqxGrid('exportdata', 'csv', 'Stock-Card'); }
			});
			
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

<body onLoad="closeWindow()">

<div style="width:100%; border:0px solid blue; font-family:Arial, Helvetica, sans-serif; font-size:12px;">
	
	<div style="border:0px solid red; float:left;">
		<table border="0">
            <tr> <td> Run Date </td> <td> : </td> <td> <?php echo date('d-m-Y'); ?> </td> </tr>
			<tr> <td> Log </td> <td> : </td> <td> <?php echo $log; ?> </td> </tr>
		</table>
	</div>
    
    <div style="border:0px solid red; float:right;">
		<table border="0">
            <tr> <td> Code </td> <td> : </td> <td> <?php echo $code; ?> </td> </tr>
            <tr> <td> Name </td> <td> : </td> <td> <?php echo $name; ?> </td> </tr>
            <tr> <td> Weight <sup>(t)</sup> </td> <td> : </td> <td> <?php echo $weight; ?> </td> </tr>
            <tr> <td> Content </td> <td> : </td> <td> <?php echo $content; ?> </td> </tr>
		</table>
	</div>

	<center>
	   <div style="border:0px solid green; width:500px;">	
	       <h4> <?php echo isset($company) ? $company : ''; ?> </h4>
           <p style="margin:5px; padding:0;"> <?php echo $address; ?> <br> Telp. <?php echo $phone1.' - '.$phone2; ?> <br>
               Website : <?php echo $website; ?> &nbsp; &nbsp; Email : <?php echo $email; ?> </p>
	   </div>
	</center> <hr>
    
    <p style="text-align:center; font-size:14pt; font-weight:bold;"> TANK STORAGE STOCK CARD </p>
	
	<div class="clear"></div>
	
	<div style="width:100%; border:0px solid brown; margin-top:20px; border:0px solid #000; ">
		
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
<th> No </th> <th> Date </th> <th> Cust </th> <th> Doctype </th> <th> Docno </th> <th> Debit </th> <th> Credit </th> <th> Balance </th> <th> Sounding </th> <th> Diff </th> <th> FFA </th> <th> Moisture </th> <th> Dirt </th>
		   </tr>
           </thead>
          
          <tbody> 
          
          <tr>
          <td></td> <td> Opening : </td> <td></td> <td></td> <td></td> <td></td> <td></td> <td> <?php echo $open; ?> </td>
          </tr>
          
		  <?php 
		  	  
			  $opentrans = $open;
			  function trans($open,$in,$out)
			  {
				  $res = floatval($in-$out);
				  return $open+$res;
			  }
              
              function sounding($date,$tank,$type){
                  $val = new Tank_sounding_lib();
                  return floatval($val->get_sounding($date,$tank,$type));
              }
              
              function diff($balance,$sounding){
                  $res = floatval($balance-$sounding);
                  return $res;
              }
			  
		      $i=1; 
			  if ($trans)
			  {
				foreach ($trans as $res)
				{	
                    
            $trans = trans($opentrans,$res->debit,$res->credit);
            $sounding = sounding($res->dates,$pid,'netkg');
                    
				   echo " 
				   <tr> 
				       <td align=\"center\" class=\"strongs\">".$i."</td> 
                       <td class=\"strongs\">".tglin($res->dates)."</td>
					   <td class=\"strongs\">".$res->cust_id."</td>
                       <td class=\"strongs\">".$res->doctype."</td>
                       <td class=\"strongs\">".$res->docno."</td>
					   <td align=\"center\" class=\"strongs\">".$res->debit."</td>
					   <td align=\"center\" class=\"strongs\">".$res->credit."</td>
					   <td align=\"center\" class=\"strongs\">".$trans."</td>
                       <td align=\"center\" class=\"strongs\">".$sounding."</td>
					   <td align=\"center\" class=\"strongs\">".diff($trans,$sounding)."</td>
                       <td align=\"center\" class=\"strongs\">".sounding($res->dates,$pid,'ffa')."</td>
                       <td align=\"center\" class=\"strongs\">".sounding($res->dates,$pid,'moisture')."</td>
                       <td align=\"center\" class=\"strongs\">".sounding($res->dates,$pid,'dirt')."</td>
				   </tr>";
				   
				   $opentrans = trans($opentrans,$res->debit,$res->credit);
				   $i++;
				}
			 } 
			  
		  ?>
          
         </tbody> 
		</table>
	</div>

</div>
<a style="float:left; margin:10px;" title="Back" href="<?php echo site_url($page); ?>"> 
  <img src="<?php echo base_url().'images/back.png'; ?>"> 
</a>
</body>
</html>
