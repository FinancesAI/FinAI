$( document ).ready(function() {
	$('#incredit-post').on("click", function () {
		event.preventDefault();
		$("#incredit-api-info").empty();
		$("#incredit-api-info").append("<img src='/images/loading.gif' class='loading'>");
		//$("#incredit-post").attr("disabled", "disabled");
		$("#incredit-api-info").removeClass("hide");
		
		$.post(window.location.href, $("#incredit-form").serialize()).done(function(data) {
			$("#incredit-api-info").empty();
			var obj = jQuery.parseJSON(data);
			$("#incredit-api-info").append(obj.decision);
			if (obj.valid) {
				$("#incredit-form").remove();
			} else {
				$("#incredit-post").removeAttr("disabled");
			}
			$("#incredit-api-info").append(obj.html);
		}, "json");
	});
});