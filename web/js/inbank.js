$( document ).ready(function() {
	$('#inbank-post').on("click", function () {
		event.preventDefault();
		$("#inbank-api-info").empty();
		$("#inbank-api-info").append("<img src='/images/loading.gif' class='loading'>");
		$("#inbank-statues").attr("disabled", "disabled");
		$("#inbank-post").attr("disabled", "disabled");
		$("#inbank-api-info").removeClass("hide");
		
		var formData = new FormData($('#inbank-form')[0]);
		$.ajax({
		  	url: window.location.href,
			data: formData,
			async: false,
		 	contentType: false,
		   	processData: false,
		   	cache: false,
		   	type: 'POST',
		   	success: function(data) {
		   		$("#inbank-api-info").empty();
				$("#inbank-api-info").append(data);
				$("#inbank-statues").removeAttr("disabled");
				$("#inbank-post").removeAttr("disabled");
				$("#inbank-file").val("");
		   	},
		});    
	});
	
	$('#inbank-status-post').on("click", function () {
		event.preventDefault();
		$("#inbank-api-info").empty();
		$("#inbank-api-info").append("<img src='/images/loading.gif' class='loading'>");
		$("#inbank-statues").attr("disabled", "disabled");
		$("#inbank-post").attr("disabled", "disabled");
		$("#inbank-api-info").removeClass("hide");
		
		$.post(window.location.href, $("#inbank-status-form").serialize()).done(function( data ) {
			$("#inbank-api-info").empty();
			$("#inbank-api-info").append(data);
			$("#inbank-statues").removeAttr("disabled");
			$("#inbank-post").removeAttr("disabled");
		}, "json");
	});
	
	$('#inbank-post-new').on("click", function () {
		event.preventDefault();
		$("#inbank-api-info").empty();
		$("#inbank-api-info").append("<img src='/images/loading.gif' class='loading'>");
		$("#inbank-statues").attr("disabled", "disabled");
		$("#inbank-post").attr("disabled", "disabled");
		$("#inbank-api-info").removeClass("hide");
		
		$.post(window.location.href, {inbankPostNew: true}).done(function( data ) {
			$("#inbank-api-info").empty();
			$("#inbank-api-info").append(data);
			$("#inbank-statues").removeAttr("disabled");
			$("#inbank-post").removeAttr("disabled");
		}, "json");
	});
});