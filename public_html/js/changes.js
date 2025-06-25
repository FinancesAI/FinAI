$(document).ready(function() {

	if ($("#changes-desc")) {
		var defaultOffset = $("#changes-desc").data("offsetdef");
		$("#changes-desc .loading").remove();
		getChanges(true);
	}
	
	if ($("#more-changes-desc")) {
		$("#more-changes-desc").on("click", function() {
			$("#changes-desc").data("offset", (parseInt($("#changes-desc").attr("data-offset"))+parseInt(defaultOffset)));
			getChanges(true);
		});
	}
	
	if ($("#changes")) {
		var defaultOffset = $("#changes").data("offsetdef");
		$("#changes .loading").remove();
		getChanges(false);
	}
	
	if ($("#more-changes")) {
		$("#more-changes").on("click", function() {
			$("#changes").attr("data-offset", (parseInt($("#changes").attr("data-offset"))+parseInt(defaultOffset)));
			getChanges(false);
		});
	}
	
	function getChanges(desc) {
		if (desc) {
			var url = $("#changes-desc").data("url");
			var type = $("#changes-desc").data("type");
			var type_id = $("#changes-desc").data("type-id");
			var offset = $("#changes-desc").data("offset");
		} else {
			var url = $("#changes").data("url");
			var type = $("#changes").data("type");
			var type_id = $("#changes").data("type-id");
			var offset = $("#changes").attr("data-offset");
		}
		
		$.ajax({
			  method: "POST",
			  url: url,
			  data: {
				  type: type,
				  type_id: type_id,
				  offset: offset,
			  },
			})
		  .done(function(data) {
			  data = jQuery.parseJSON(data);
			  if (data.html) {
					if (desc) {
						  $("#changes-desc .list-group").append(data.html);
						  if (data.more) {
							  $("#more-changes-desc").removeClass("hide");
						  } else {
							  $("#more-changes-desc").addClass("hide");
						  }
					} else {
						  $("#changes .list-group").append(data.html);
						  if (data.more) {
							  $("#more-changes").removeClass("hide");
						  } else {
							  $("#more-changes").addClass("hide");
						  }
					}
			  }  else {
				  $("#more-changes").addClass("hide");
			  }
		  }).fail(function() {
		    alert( "error getting changes history" );
		  });
	}
	
	$("#changes-all-btn").click(function() {
		if (!$("#changes-all").hasClass("loaded")) {
			$("#changes-all").addClass("loaded");
			$("#changes-all .loading").remove();
			getChangesAll();
		}
	});

	$("#more-changes-all").on("click", function() {
		$("#changes-all").attr("data-offset", (parseInt($("#changes-all").attr("data-offset"))+parseInt(defaultOffset)));
		getChangesAll();
	});
	
	function getChangesAll() {
		$.ajax({
			  method: "POST",
			  url: $("#changes-all").data("url"),
			  data: {
				  type: $("#changes-all").data("type"),
				  type_id: $("#changes-all").data("type-id"),
				  offset: $("#changes-all").attr("data-offset"),
			  },
			})
		  .done(function(data) {
			  data = jQuery.parseJSON(data);
			  if (data.html) {
				  $("#changes-all .list-group").append(data.html);
				  if (data.more) {
					  $("#more-changes-all").removeClass("hide");
				  } else {
					  $("#more-changes-all").addClass("hide");
				  }
			  }  else {
				  $("#more-changes-all").addClass("hide");
			  }
		  }).fail(function() {
		    alert( "error getting all changes history" );
		  });
	}

	function getInputTexts() {
		return Array.from(document.querySelectorAll('.loan-view input[type="text"].form-control'));
	}

	function copyFieldValueOnDoubleClick() {
    getInputTexts().forEach(function (el) {
      el.ondblclick = copyValue;
    });

  }

  function copyValue() {
    document.execCommand('copy');
  }

  copyFieldValueOnDoubleClick();


});

