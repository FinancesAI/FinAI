$( document ).ready(function() {
	$('#aizdevums-post').on("click", function () {
		event.preventDefault();
		$("#aizdevums-api-info").empty();
		$("#aizdevums-api-info").append("<img src='/images/loading.gif' class='loading'>");
		$("#aizdevums-post").attr("disabled", "disabled");
		$("#aizdevums-api-info").removeClass("hide");
		$.post(window.location.href, $(".aizdevums-forma form").serialize()).done(function(data) {
			$("#aizdevums-api-info").empty();
			var obj = jQuery.parseJSON(data);
			if (obj.valid) {
				$("#aizdevums-form").remove();
				location.reload();
			} else {
				$("#aizdevums-post").removeAttr("disabled");
			}
			$("#aizdevums-api-info").append(obj.html);
		}, "json");
	});
	
    $('#sakrit-ar-dzivesvietu').change(function () {
        if ($(this).is(':checked') == true) {
            $('.d1').val($('.f1').val());
            $('.d2').val($('.f2').val());
            $('.d3').val($('.f3').val());
            $('.d4').val($('.f4').val());
            $('.d5').val($('.f5').val());
            $('.d6').val($('.f6').val());
        }
    });
});


function callFunction(clicked_id) {
	$("#info").empty();
	var functionId = clicked_id;
	if (functionId == 'UPLOADCONTRACT') {
		document.getElementById("loading").innerHTML = 'Lūdzu uzgaidiet, kamēr tiek apstrādāts pieprasījums!';
	}
	
	var appNumberValue = $("#appNumber").val();
	var contractNoAndSocSecNo = $("#contractNoAndSocSecNo").val();
	var contractNr = $("#contractNo").val();
	var webAppNr = $("#webAppNo").val();
	var request = $.ajax({
		url: window.location.href,
		data: {action: functionId, appNumber: appNumberValue, secNo: contractNoAndSocSecNo, contractNo: contractNr, webAppNo: webAppNr},
		type: "POST",			
		dataType: "text"
	});
	
	
	request.done(function(data) {
		var obj = jQuery.parseJSON(data);
		document.getElementById("loading").innerHTML = "";
		document.getElementById("info").innerHTML = obj.html;
		
		if (obj.refresh) {
			setTimeout(function() {
				location.reload();
			}, 3000);
		}
	});		
	
}
