$( document ).ready(function() {
	$('#uno-post').on("click", function () {
		event.preventDefault();
		$("#uno-api-info").empty();
		$("#uno-api-info").append("<img src='/images/loading.gif' class='loading'>");
		$("#uno-post").attr("disabled", "disabled");
		$("#uno-api-info").removeClass("hide");
		
		$.post(window.location.href, $("#uno-form").serialize()).done(function( data ) {
			$("#uno-api-info").empty();
			var obj = jQuery.parseJSON(data);
			$("#uno-api-info").append(obj.decision);
			if (!obj.error) {
				$("#uno-form").remove();
			} else {
				$("#uno-post").removeAttr("disabled");
				$("#uno-api-info").append("Kļūme, pārbaudiet laukus.");
			}
		}, "json");
	});
});