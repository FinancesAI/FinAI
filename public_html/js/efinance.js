$( document ).ready(function() {
	$('#efinance-post').on("click", function () {
		event.preventDefault();
		$("#efinance-api-info").empty();
		$("#efinance-api-info").append("<img src='/images/loading.gif' class='loading'>");
		$("#efinance-post").attr("disabled", "disabled");
		$("#efinance-api-info").removeClass("hide");
		
		$.post(window.location.href, $("#efinance-form").serialize()).done(function( data ) {
			$("#efinance-api-info").empty();
			if (data == "ok") {
				$("#efinance-form").remove();
				$("#efinance-api-info").append("<p>Sent!</p>");
			} else {
				$("#efinance-post").removeAttr("disabled");
				$("#efinance-api-info").append("<p>"+data+"</p>");
			}
		}, "json");
	});
});