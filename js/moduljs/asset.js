$(document).ready(function (e) {
	
	$('#datatable-buttons').dataTable({
	 dom: 'T<"clear">lfrtip',
		tableTools: {"sSwfPath": site}
	 });
	
    // function general
    load_data();
	
	// reset form
	$("#breset,#bclose").click(function(){
	   resets();
	});

	$('#ds1,#ds2').daterangepicker({
        locale: {format: 'YYYY-MM-DD'},
		singleDatePicker: true,
        showDropdowns: true
	});
	
	// group status
	$(document).on('change','#cgroup',function(e)
	{	
		e.preventDefault();
		var val = $(this).val();
		var url = sites +"/get_period_group/"+val;
		
		$.ajax({
			type: 'GET',
			url: url,
			cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
				if (val){ $("#tperiod").val(result); 
				$("#tmonths").val(parseInt(result*12));
				}else{ $("#tperiod,#tmonths").val('0'); }
				// $("#tcost").val('');
			}
		})
		return false;	
	});

	$(document).on('change','#cgroup_update',function(e)
	{	
		e.preventDefault();
		var val = $(this).val();
		var url = sites +"/get_period_group/"+val;
		
		$.ajax({
			type: 'GET',
			url: url,
			cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
				if (val){ $("#tperiod_update").val(result); 
				$("#tmonths_update").val(parseInt(result*12));
				}else{ $("#tperiod_update,#tmonths_update").val('0'); }
				// $("#tcost_update").val('');
			}
		})
		return false;	
	});

	$('#bcalculate').click(function() {
      
        var purchase_amt = parseInt($("#tamount").val());
        var residual = parseInt($("#tresidual").val());
        var total_month = parseInt($("#tmonths").val());
        if (residual < purchase_amt){
            
            var amt = Math.round((purchase_amt-residual)/total_month);
            $("#tcost").val(amt);
            
        }else{ $("#tresidual").val(''); }
	});

	$('#bcalculate_update').click(function() {
      
        var purchase_amt = parseInt($("#tamount_update").val());
        var residual = parseInt($("#tresidual_update").val());
        var total_month = parseInt($("#tmonths_update").val());
        if (residual < purchase_amt){
            
            var amt = Math.round((purchase_amt-residual)/total_month);
            $("#tcost_update").val(amt);
            
        }else{ $("#tresidual_update").val(''); }
	});
	
	// fungsi jquery update
	$(document).on('click','.text-primary',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_get +"/"+ del_id;
		$(".error").fadeOut();
		
		$("#myModal2").modal('show');
		// batas
		$.ajax({
			type: 'POST',
			url: url,
    	    cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
								
				res = result.split("|");

				$("#tid_update").val(res[0]);
				$("#tcode").val(res[1]);
				$("#tname").val(res[2]);
				$("#cgroup_update").val(res[3]).change();
				$("#tdesc").val(res[4]);
				$("#ds2").val(res[5]);
				$("#tperiod_update").val(res[6]);
				$("#tamount_update").val(res[7]);
				$("#tresidual_update").val(res[8]);
				$("#tcost_update").val(res[9]);
				$("#tmonths_update").val(res[10]);
				$("#titem1").val(res[11]);
			}
		})
		return false;	
	});

		// fungsi jquery ledger
	$(document).on('click','.text-invoice',function(e){	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites+"/invoice/"+ del_id;
		window.open(url, "_blank", "scrollbars=1,resizable=0,height=600,width=800");
	});
	
	// publish status
	$(document).on('click','.primary_status',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_primary +"/"+ del_id;
		$(".error").fadeOut();
		
		// $("#myModal2").modal('show');
		// batas
		$.ajax({
			type: 'POST',
			url: url,
    	    cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
				
				res = result.split("|");
				if (res[0] == "true")
				{   
			        error_mess(1,res[1],0);
					load_data();
				}
				else if (res[0] == 'warning'){ error_mess(2,res[1],0); }
				else{ error_mess(3,res[1],0); }
			}
		})
		return false;	
	});

	// default status
	$(document).on('click','.default_status',function(e)
	{	
		e.preventDefault();
		var element = $(this);
		var del_id = element.attr("id");
		var url = sites_default +"/"+ del_id;
		$(".error").fadeOut();
		// batas
		$.ajax({
			type: 'POST',
			url: url,
    	    cache: false,
			headers: { "cache-control": "no-cache" },
			success: function(result) {
				res = result.split("|");
				if (res[0] == "true")
				{   
			        error_mess(1,res[1],0);
					load_data();
				}
				else if (res[0] == 'warning'){ error_mess(2,res[1],0); }
				else{ error_mess(3,res[1],0); }
			}
		})
		return false;	
	});
	
		
// document ready end	
});

    function resets()
    {
	  $(document).ready(function (e) {
		  
		 $("#tname,#uploadImage").val("");
		 $("#catimg,#catimg_update").attr("src","");
	  });
    }

// fungsi load data
	function load_data()
	{
		$(document).ready(function (e) {
			
			var oTable = $('#datatable-buttons').dataTable();
			var stts = 'btn btn-danger';
			var stts1 = 'btn btn-danger';

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
						$(".chkselect").remove();
							
		$("#chkbox").append('<input type="checkbox" name="newsletter" value="accept1" onclick="cekall('+s.length+')" id="chkselect" class="chkselect">');
							
							for(var i = 0; i < s.length; i++) {
							if (s[i][7] == 1){ stts = 'btn btn-success'; }else { stts = 'btn btn-danger'; }
							 oTable.fnAddData([
'<input type="checkbox" name="cek[]" value="'+s[i][0]+'" id="cek'+i+'" style="margin:0px"  />',
										i+1,
										s[i][1],
										s[i][2],
										s[i][3],
										s[i][4],
										s[i][5],
										s[i][6],
'<div class="btn-group" role"group">'+
'<a href="" class="'+stts+' btn-xs primary_status" id="' +s[i][0]+ '" title="Publish Status"> <i class="fa fa-power-off"> </i> </a>'+
'<a href="" class="btn btn-warning btn-xs text-invoice" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-book"> </i> </a>'+
'<a href="" class="btn btn-primary btn-xs text-primary" id="' +s[i][0]+ '" title=""> <i class="fa fas-2x fa-edit"> </i> </a>'+
'<a href="#" class="btn btn-danger btn-xs text-danger" id="'+s[i][0]+'" title="delete"> <i class="fa fas-2x fa-trash"> </i> </a>'+
'</div>'
											   ]);										
											} // End For
											
											
				},
				error: function(e){
				   console.log(e.responseText);	
				   oTable.fnClearTable(); 
				}
				
			});  // end document ready	
			
        });
	}
	// batas fungsi load data