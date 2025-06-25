$( document ).ready(function() {
	$('#tfbank-has').on("click", function () {
		$("#tfbank-has-target").toggle();
	});
	
	$('#tfbank-post').on("click", function () {
		event.preventDefault();
		$("#tfbank-api-info").empty();
		$("#tfbank-api-info").append("<img src='/images/loading.gif' class='loading'>");
		$("#tfbank-post").attr("disabled", "disabled");
		$("#tfbank-api-info").removeClass("hide");
		
		$.post(window.location.href, $("#tfbank-form").serialize()).done(function(data) {
			$("#tfbank-api-info").empty();
			var obj = jQuery.parseJSON(data);
			$("#tfbank-api-info").append(obj.decision);
			if (obj.valid) {
				$("#tfbank-form").remove();
				window.setTimeout(function(){location.reload()},3000);
			} else {
				$("#tfbank-post").removeAttr("disabled");
			}
			$("#tfbank-api-info").append(obj.html);
		}, "json");
	});
	
	$('#tfbank-post23').on("click", function () {
		event.preventDefault();
		$("#tfbank-api-info").empty();
		$("#tfbank-api-info").append("<img src='/images/loading.gif' class='loading'>");
		$("#tfbank-post2").attr("disabled", "disabled");
		$("#tfbank-api-info").removeClass("hide");
		
		$.post(window.location.href, $("#tfbank-form").serialize()).done(function(data) {
			$("#tfbank-api-info").empty();
			var obj = jQuery.parseJSON(data);
			$("#tfbank-api-info").append(obj.decision);
			if (obj.valid) {
				$("#tfbank-post2").removeAttr("disabled");
			} else {
				$("#tfbank-post2").removeAttr("disabled");
			}
			$("#tfbank-api-info").append(obj.html);
		}, "json");
	});
});