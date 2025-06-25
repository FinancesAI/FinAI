function appBaseUrl() {
    return $('meta[name=baseUrl]').attr("content");
}

var sound = new Howl({
    src: [appBaseUrl() + 'sounds/alert.mp3', appBaseUrl() + 'sounds/alert.ogg', appBaseUrl() + 'sounds/alert.mp4'],
    preload: true,
});

var newMessages = $('.messages-new');

$( document ).ready(function() {

	$( ".context-menu-click" ).contextmenu(function() {
		event.preventDefault();
		window.open($(event.target.parentElement).data("link"), "_blank");
		setTimeout(function() { window.focus() },500);

	});
	
	$(".stats-load").click(function(e) {
		e.preventDefault();
		$.post(window.location.href, { 'task': 'load-more', 'offset': $(this).data("offset")}).done(function( data ) {
			if (data) {
				$("#stats-more").append(data);
				$(".stats-load").data("offset", $(".stats-load").data("offset")+3);
			} else {
				$(".stats-load").hide();
			}
		}.bind(this));
	});

	$("#set-rem").click(function() {
		$(".loan-rm-time-fx").toggleClass("hide");
		if (!$(".loan-app-time-fx").hasClass("hide")) {
			$(".loan-app-time-fx").addClass("hide");
		}
	});
	
	$("#set-app").click(function() {
		$(".loan-app-time-fx").toggleClass("hide");
		if (!$(".loan-rm-time-fx").hasClass("hide")) {
			$(".loan-rm-time-fx").addClass("hide");
		}
	});
	
	$(".in-progress-loan").click(function (e) {
		return false;
	});
	
	$(".appointment-rm").click(function() {
    	var id = $(this).text();

		$.post(window.location.href, { 'task': 'remove-app', 'id': id}).done(function( data ) {
			if (data == 1) {
				$("#custom-app-time-val").val("");
				$("#custom-app-time-val").attr("placeholder", $("#custom-app-time-val").data("placeholder"));
				$("#"+id).remove();
		    } else {
		    	alert("Error: not updated");
		    }
		}.bind(this));
	});
	
	$(".btn-prog-save").click(function() {
    	var status = $(this).prev().prev().prev().prev().prev().val();
    	var amount = $(this).prev().prev().prev().val();
    	var text = $(this).prev().val();
    	var provider = $(this).data("provider");
    	var btn = $(this).parent().prev();
    	$(this).attr("disabled", "disabled");

		$.post(window.location.href, { 'task': 'save-progress', 'status': status, 'text':text, 'amount':amount, 'provider':provider }).done(function( data ) {
			var obj = jQuery.parseJSON(data);
			$(this).removeAttr("disabled", "");
			if (obj.save == 1) {
				$(btn).removeClass()
				$(btn).addClass("in-progress-loan btn "+obj.class);
		    } else {
		    	alert("Error: not updated");
		    }
		}.bind(this));
	});
	
	$('body').tooltip({
	    selector: '[data-toggle="tooltip"]'
	});
    $(".dropdown-toggle").click(function() {
		if ($(this).attr("href") && $(this).attr("href") !== "#" && $( window ).width() > 765) {
			window.location.href = $(this).attr("href");
		}
	});

	$(".global-filters input").on('change', function () {
		var cookieString = "";
		$(".global-filters input:checked").each(function( index ) {
			cookieString = cookieString+","+$(this).data("id");
		});
		cookieString = cookieString.substring(1);
		if (cookieString.indexOf("0") >= 0) { cookieString = "0"; }
	    document.cookie = "global_filter="+cookieString;

	    if (cookieString == "0") {
			$(".global-filters input").each(function( index ) {
				$(this).prop('checked', false);
			});
			$($(".global-filters input")[0]).prop('checked', true);
	    }
	});
	
	$(".global-filters-op").on('change', function () {
		
		if ($(this).is(":checked")) {
		    document.cookie = "global_filter_op=1";
		} else {
		    document.cookie = "global_filter_op=0";
		}

	   
	});

	$("#mail-box").on('change', function () {
		var selected = $("#mail-box").val();
		
		if (selected == "5") {
			$("#mail-5").removeClass("hide");
			$("#mail-9").addClass("hide");
		} else if (selected == "9") {
			$("#mail-9").removeClass("hide");
			$("#mail-5").addClass("hide");
			$("#email-custom-text").removeClass("hide");
			$("#email-custom-text-label").removeClass("hide");
		} else if (selected == "26") {
			$("#mail-9").removeClass("hide");
			$("#mail-5").addClass("hide");
			$("#email-custom-text").addClass("hide");
			$("#email-custom-text-label").addClass("hide");
		} else if (selected == "11") {
			$("#mail-9").removeClass("hide");
			$("#mail-5").addClass("hide");
			$("#email-custom-text").addClass("hide");
			$("#email-custom-text-label").addClass("hide");
		} else {
			$("#mail-9").addClass("hide");
			$("#mail-5").addClass("hide");
		}
	});	
	
	$("#sms-box").on('change', function () {
		var selected = $("#sms-box").val();
		
		if (selected == "4") {
			$("#sms-4").removeClass("hide");
		} else {
			$("#sms-4").addClass("hide");
		}
	});
	
	$('.in-progress-loan').on("click", function () {
		$('.in-progress-loan').removeClass("active");
		$(this).addClass("active");
		$.post(window.location.href, { 'Loan[approved]': $(this).data("id") }).done(function( data ) {
			if (data == 1) {
		    	
		    } else {
		    	alert("Error: not updated");
		    }
		});
	});
	
	$('#gmail-email-list-btn.need-load').on("click", function () {
		if ($("#gmail-email-list-btn").hasClass("need-load")) {
			$.post(app.gmailUrl+"?id="+$(this).attr('data-id')).done(function( data ) {
			    if (data) {
			    	$("#gmail-email-list").empty().append(data);
			    	console.log("loaded");
			    } else {
			    	$("#gmail-email-list").empty().append("Failed to load data");
			    }
			});
		}
		
		$("#gmail-email-list-btn").removeClass("need-load");
	});
	
	$('#bill-danger-reminder').on("click", function () {
		$( "body" ).append( "<div id='check-status-bg'></div>" +
		"<div id='check-status'>" +
		"<h2>ARE YOU SURE?</h2>" +
		"<button id='check-status-no' class='btn btn-primary'>NO</button>" +
		"<button id='check-status-yes' class='btn btn-primary'>YES</button>" +
		"</div>" );

		$( "#check-status-no" ).click(function() {
			$("#check-status-bg").fadeOut(300, function() { $(this).remove(); });
			$("#check-status").fadeOut(300, function() { $(this).remove(); });
			console.log("cancel");
		});
		
		$( "#check-status-yes" ).click(function() {
			$("#check-status-bg").fadeOut(300, function() { $(this).remove(); });
			$("#check-status").fadeOut(300, function() { $(this).remove(); });
			
			if ($(this).attr('id') == "bill-danger-reminder") {
				$.post(app.mailUrl, { id: $(this).attr('data-id'), type: "20" }).done(function( data ) {
				    if (data) {
				    	alert("Email send");
				    } else {
				    	alert("Email not send");
				    }
				});
				
				$.post(app.smsUrl, { id: $(this).attr('data-id'), type: "8" }).done(function( data ) {
				    if (data) {
				    	alert("Sms send");
				    } else {
				    	alert("Sms not send");
				    }
				});
			}

		}.bind(this));
	});
	
	$('.btn-rm').on("click", function (event) {
		event.preventDefault();
		updateReminderTime($(this).data("time"), $(this).data("cl"));
	});
	
	if ($('#custom-rm-time-val').length) {
		jQuery('#custom-rm-time-val').datetimepicker({
		    format: 'unixtime',
		    value: $('#datepicker-remindertime-label').data("value"),
		    class: 'form-control',
		    id: 'datepicker-remindertime-label',
		    onChangeDateTime:function(dp,$input){
		    	$("#custom-rm-time-val").val("");
		    },
		    onClose:function(dp,$input){
		    	updateReminderTime($input.val(), 0);
		        $("#custom-rm-time-val").val("");
		    }
		});
	}
	
	if ($('#custom-app-time-val').length) {
		jQuery('#custom-app-time-val').datetimepicker({
		    format: 'unixtime',
		    value: $('#datepicker-apptime-label').data("value"),
		    class: 'form-control',
		    id: 'datepicker-apptime-label',
		    onChangeDateTime:function(dp,$input){
		    	$("#custom-app-time-val").val("");
		    },
		    onClose:function(dp,$input){
		    	updateAppTime($input.val(), 0);
		        $("#custom-app-time-val").val("");
		    }
		});
	}
	
	$('#datepicker-remindertime-label').on("click", function () {
		jQuery('#datepicker-remindertime-label').datetimepicker({
		    format: 'unixtime',
		    value: $('#datepicker-remindertime-label').data("value"),
		    class: 'form-control',
		    id: 'datepicker-remindertime-label',
		    onChangeDateTime:function(dp,$input){
		        $("#datepicker-remindertime").val($input.val());
		    }
		});
	});
	
	$('#datepicker-remindertime-remove').on("click", function () {
		$("#datepicker-remindertime").val("");
	});

	$('#datepicker-closetime-label').on("click", function () {
		console.log("clicked");
		jQuery('#datepicker-closetime-label').datetimepicker({
		    format: 'unixtime',
		    value: $('#datepicker-closetime-label').data("value"),
		    class: 'form-control',
		    id: 'datepicker-closetime-label',
		    onChangeDateTime:function(dp,$input){
		        $("#datepicker-closetime").val($input.val());
		    }
		});
	});
	
	$('#datepicker-closetime-remove').on("click", function () {
		$("#datepicker-closetime").val("");
	});
	
	$('#checkAll').on("change", function () {
		$('#checkAllCheck input[type=checkbox]').trigger('click'); 
	});
	
	if(typeof(dealStages) != "undefined" && dealStages !== null)
		new dropdownRelation($("#deal"), $("#deal-product"), dealStages, dealStagesProducts);
	
	new loanStatusCheck();
	
	window.setTimeout(function() { $(".alert-fixed").alert('close'); }, 3000);
    window.setTimeout(updateNewMessagesCount, 3000);

	$('#loan-actions option').mousedown(function(e) {
	    e.preventDefault();
	    $(this).prop('selected', $(this).prop('selected') ? false : true);
	    return false;
	});

  changeLoanColor();

	
	$(function () {
		  $('[data-toggle="tooltip"]').tooltip();
	});
}); // end ready

function changeLoanColor() {
	'use strict';

  var changeColorButtons = $('.js--change-color');
  changeColorButtons.each(function (e) {
		var $this = $(this);
    $this.on('click', changeActiveColor);
  });
}

function changeActiveColor(e) {
  'use strict';
  e.preventDefault();

  if (!e.target.classList.contains('active')) {
    $('.js--change-color').removeClass('active');
    e.target.classList.add('active');
    changeInputValue(e.target);
  }
}

function changeInputValue(el) {
  'use strict';
  el.parentNode.querySelector('input[name="Loan[color_id]"]').value = el.dataset.value;
}

function updateReminderTime(time, cl) {
	if (time == "") {
		return;
	}
	$('#custom-rm-time-val').attr("disabled", "disabled");
	$('.btn-rm').attr("disabled", "disabled");
	$.post(window.location.href, { 'reminder_time': time, 'reminder_class': cl }).done(function( data ) {
		$('#custom-rm-time-val').attr("placeholder", data);
		$('#custom-rm-time-val').removeAttr("disabled", "");
		$('.btn-rm').removeAttr("disabled", "");
	});
}

function updateAppTime(time, cl) {
	if (time == "") {
		return;
	}
	$('#custom-app-time-val').attr("disabled", "disabled");
	$('.btn-rm').attr("disabled", "disabled");
	$.post(window.location.href, { 'app_time': time}).done(function( data ) {
		$('#custom-app-time-val').attr("placeholder", data);
		$('#custom-app-time-val').removeAttr("disabled", "");
		$('.btn-rm').removeAttr("disabled", "");
	});
}

function changeToggle(element) {
	if ($(element).next().hasClass("hide")) {
		$(element).next().removeClass("hide");
		$(element).removeClass("glyphicon glyphicon-chevron-down");
		$(element).addClass("glyphicon glyphicon-chevron-up");
	} else {
		$(element).next().addClass("hide");
		$(element).removeClass("glyphicon glyphicon-chevron-up");
		$(element).addClass("glyphicon glyphicon-chevron-down");
	}
}

function dropdownRelation(primaryElement, secondaryElement, primaryArray, secondaryArray)
{
	this.setup = function ()
	{
		primaryElement.on('change', function() {
			secondaryElement.empty();
			$.each(secondaryArray[primaryElement.val()], function( index, value ) {
				secondaryElement.append($('<option>', { value : this.id }).text(this.title));
			});
		});
	}
	
	this.setup();
}

function loanStatusCheck ()
{
    var previous;

    $("select[name='Loan[status]']").on('focus', function () {
        previous = this.value;
    }).change(function() {
    	if ($(this).val() == 5 && previous !== 5) {
    		$( "body" ).append( "<div id='check-status-bg'></div>" +
    		"<div id='check-status'>" +
    		"<h2>ARE YOU SURE?</h2>" +
    		"<button id='check-status-no' class='btn btn-primary'>NO</button>" +
    		"<button id='check-status-yes' class='btn btn-primary'>YES</button>" +
    		"</div>" );

    		$( "#check-status-no" ).click(function() {
    			$("select[name='Loan[status]']").val(previous)
    			$("#check-status-bg").fadeOut(300, function() { $(this).remove(); });
    			$("#check-status").fadeOut(300, function() { $(this).remove(); });
    		});
    		
    		$( "#check-status-yes" ).click(function() {
    			$("select[name='Loan[status]']").val(5);
    			$("#check-status-bg").fadeOut(300, function() { $(this).remove(); });
    			$("#check-status").fadeOut(300, function() { $(this).remove(); });
    			$("#notes-tab-update").trigger("click");
    		});
    	} else if ($(this).val() == 4 && previous !== 4) {
    		$( "body" ).append( "<div id='check-status-bg'></div>" +
	    		"<div id='check-status'>" +
	    		"<h2>SEND REJECT EMAIL?</h2>" +
	    		"<button id='check-status-no' class='btn btn-primary'>NO</button>" +
	    		"<button id='check-status-yes' class='btn btn-primary'>YES</button>" +
	    		"</div>" );

	    		$( "#check-status-no" ).click(function() {
	    			$("select[name='Loan[status]']").val(4);
	    			$("#check-status-bg").fadeOut(300, function() { $(this).remove(); });
	    			$("#check-status").fadeOut(300, function() { $(this).remove(); });
	    		});
    	    		
	    		$( "#check-status-yes" ).click(function() {
	    			$( "#check-status-yes" ).attr("disabled", "disabled");
	    			$( "#check-status-no" ).attr("disabled", "disabled");
	    			

	    			$.ajax({
	    				  method: "POST",
	    				  url: app.mailUrl,
	    				  data: {"id" : app.loan.id, "type": 19, "custom": true},
	    			})
	    			.done(function(data) {
	    				if (data == 1) {

	    				} else {
		    				alert("EMAIL not sent.");
	    				}
	    				
	  	    			$("select[name='Loan[status]']").val(4);
		    			$("#check-status-bg").fadeOut(300, function() { $(this).remove(); });
		    			$("#check-status").fadeOut(300, function() { $(this).remove(); });
		    			$("#notes-tab-update").trigger("click");
    			  	})
    			  	.fail(function() {
    					alert("Error connecting.");
    				});
	    		});
    	} else {
            previous = this.value;
    	}
    });
}

window.checkRedirect = function checkRedirect(element, link) {
	if (event.target.localName === "td") {
		window.location.href = link;
	}
}

function updateNewMessagesCount() {
    $.ajax({
        url: appBaseUrl() + 'chat/messages/new-messages-count',
        beforeSend: function (jqXHR) {
            if (!newMessages.length) {
                return false;
            }
        }
    }).done(function (data, textStatus, jqXHR) {

        if (data && data > 0) {

            newMessages.removeClass('hidden');

            if (data > newMessages.data("count")) {
                sound.play();
            }

            newMessages.text(data);
            newMessages.data("count", data);
        } else {
            newMessages.addClass('hidden');
        }
    }).always(function () {
        window.setTimeout(updateNewMessagesCount, 3000);
    });
}