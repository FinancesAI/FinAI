$(document).ready(function() {
	$(".grid-view table tr").each(function( index ) {
		if ($(this).data("key")) {
			
			$(this).append("<input type='checkbox' id='reject-loan-"+$(this).data('key')+"' class='reject-loan' data-id='"+$(this).data('key')+"'>");
			
			if ($(this).data('status') == 4) {
				$("#reject-loan-"+$(this).data('key')).attr("checked", "checked");
				$("#reject-loan-"+$(this).data('key')).attr("disabled", "disabled");
			}
			
			$("#reject-loan-"+$(this).data('key')).on("change", function() {
				$.ajax({
					  method: "POST",
					  url: app.baseUrl+"/loan/view?id="+$(this).data("id"),
					  data: {
						    Loan : {
						    	status: 4
						    },
					  	},
					})
				  .done(function(data) {
						$("#reject-loan-"+$(this).data('key')).attr("disabled", "disabled");
				  }).fail(function() {
				  	alert( "Error updating status" );
				  });
			});
		}
	});
});