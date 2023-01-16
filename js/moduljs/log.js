$(document).ready(function (e) {
	
    // function general
	
	$('#datatable-buttons').dataTable({
	 dom: 'T<"clear">lfrtip',
		tableTools: {"sSwfPath": site}
	 });
	 
	 // date time picker
	$('#d1,#d2,#d3,#d4,#d5').daterangepicker({locale: {format: 'YYYY/MM/DD'}});

	$('#ds1,#ds2,#ds3').daterangepicker({
		locale: {format: 'YYYY-MM-DD'},
		singleDatePicker: true,
		showDropdowns: true
 });
	
	load_data();  

			// fungsi jquery update
		$(document).on('click','.text-ledger',function(e)
		{	
			e.preventDefault();
			var element = $(this);
			var del_id = element.attr("id");
			var url = sites_invoice +"/"+ del_id;
			
			window.open(url, "_blank", "scrollbars=1,resizable=0,height=600,width=800");
		});
	
	// batas dtatable
	
	// fungsi jquery update
	$(document).on('click','.text-primary',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_get +"/"+ del_id;
		
		$("#myModal2").modal('show');
		$.post(url,
			{id:$(this).attr('data-id')},
			function(result)
			{
			   res = result.split("|");
				
			   $("#tid_update").val(res[0]);
			   $("#tusername_update").val(res[1]);
			   $("#tname_update").val(res[2]);
			   $("#taddress_update").val(res[3]);
			   $("#tphone_update").val(res[4]);
			   $("#ccity_update").val(res[5]);
			   $("#tmail_update").val(res[6]);
			   $("#crole_update").val(res[7]);
			   
			   // rstatus
			   if (res[8] == 1){ $("#rstatus1").prop( "checked", true );  }
			   else { $("#rstatus0").prop( "checked", true ); }
			}   
		);
		
	});

	$('#searchform').submit(function() {
		
		var logid = $("#tlog_search").val();
		var date = $("#ds1").val();
		var user = $("#cuser_search").val();
		var activity = $("#cactivity_search").val();
		var modul = $("#cmodul_search").val();
		var param = ['searching',logid,user,activity,modul,date];
		
		$.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data:  new FormData(this),
			contentType: false,
      cache: false,
			processData:false,
			success: function(data) {
				
				if (!param[1]){ param[1] = 'null'; }
				if (!param[2]){ param[2] = 'null'; }
				if (!param[3]){ param[3] = 'null'; }
				if (!param[4]){ param[4] = 'null'; }
				if (!param[5]){ param[5] = 'null'; }
				load_data_search(param);
			}
		})
		return false;
		swal('Error Load Data...!', "", "error");
		
	});

		
// document ready end	
});

// load data search
function load_data_search(search=null)
{
	$(document).ready(function (e) {
		
		var oTable = $('#datatable-buttons').dataTable();
		var stts = 'btn btn-danger';
		
			console.log(source+"/"+search[0]+"/"+search[1]+"/"+search[2]+"/"+search[3]+"/"+search[4]+"/"+search[5]);

			$.ajax({
			type : 'GET',
			url: source+"/"+search[0]+"/"+search[1]+"/"+search[2]+"/"+search[3]+"/"+search[4]+"/"+search[5],
			//force to handle it as text
			contentType: "application/json",
			dataType: "json",
			success: function(s) 
			{   
						//  console.log(s);
					
					oTable.fnClearTable();
					$(".chkselect").remove()

	$("#chkbox").append('<input type="checkbox" name="newsletter" value="accept1" onclick="cekall('+s.length+')" id="chkselect" class="chkselect">');
						
			for(var i = 0; i < s.length; i++) {
				oTable.fnAddData([
		'<input type="checkbox" name="cek[]" value="'+s[i][0]+'" id="cek'+i+'" style="margin:0px"  />',
							i+1,
							s[i][0],
							s[i][1],
							s[i][2],
							s[i][3],
							s[i][4],
							s[i][5],
							s[i][6],
							'<a href="" class="btn btn-primary btn-xs text-ledger" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-book"> </i> </a> '+
'<a href="#" class="btn btn-danger btn-xs text-danger" id="'+s[i][0]+'" title="delete"> <i class="fa fas-2x fa-trash"> </i> </a>'
									]);										
								} // End For 
										
			},
			error: function(e){
				 oTable.fnClearTable();  
				 //console.log(e.responseText);	
			}
			
		});  // end document ready	
		
			});
}

  // fungsi load data
	function load_data()
	{
		$(document).ready(function (e) {
			
			var oTable = $('#datatable-buttons').dataTable();
			
		    $.ajax({
				type : 'GET',
				url: source,
				//force to handle it as text
				contentType: "application/json",
				dataType: "json",
				success: function(s) 
				{   
				       console.log(s);
					  
						oTable.fnClearTable();
						$(".chkselect").remove()
	
		$("#chkbox").append('<input type="checkbox" name="newsletter" value="accept1" onclick="cekall('+s.length+')" id="chkselect" class="chkselect">');
							
							for(var i = 0; i < s.length; i++) {
						  oTable.fnAddData([
'<input type="checkbox" name="cek[]" value="'+s[i][0]+'" id="cek'+i+'" style="margin:0px"  />',
										i+1,
										s[i][0],
										s[i][1],
										s[i][2],
										s[i][3],
										s[i][4],
										s[i][5],
										s[i][6],
'<a href="" class="btn btn-primary btn-xs text-ledger" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-book"> </i> </a> '+
'<a href="#" class="btn btn-danger btn-xs text-danger" id="'+s[i][0]+'" title="delete"> <i class="fa fas-2x fa-trash"> </i> </a>'
										    ]);										
											} // End For 
											
				},
				error: function(e){
				   oTable.fnClearTable();  
				   //console.log(e.responseText);	
				}
				
			});  // end document ready	
			
        });
	}
	
	// batas fungsi load data
	function resets()
	{  
	   $(document).ready(function (e) {
		  // reset form
		  $("#tname, #tmail, #tusername, #tpassword, #ccity, #tphone, #crole, #rstatus, #taddress").val("");
	  });
	}
	