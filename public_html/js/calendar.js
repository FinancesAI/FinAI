	$(document).ready(function() {
		
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month'
				//right: 'month,agendaWeek,agendaDay'
			},
			defaultDate: defaultDate,
			droppable: false,
			selectable: true,
			selectHelper: true,
			eventRender: function(event, element) {
	            element.append( "<span class='closeon'>X</span>" );
	            element.find(".closeon").click(function() {
					$.ajax({
						  method: "POST",
						  url: window.location.href,
						  data: {
							  id: event._id,
						  },
						})
					  .done(function() {
			               $('#calendar').fullCalendar('removeEvents',event._id);
					  }).fail(function() {
					    alert( "error" );
					  });
	            });
	        },
			select: function(start, end) {
				var title = prompt('Event Title:');
				var eventData;
				if (title) {
					eventData = {
						title: title,
						start: start,
						end: end
					};

				$.ajax({
					  method: "POST",
					  url: window.location.href,
					  data: {
						  title: eventData.title,
						  start: eventData.start.unix(),
						  end: eventData.end.unix(),
					  },
					})
				  .done(function(response) {
					  eventData._id = response;
					  $('#calendar').fullCalendar('renderEvent', eventData, true);
				  }).fail(function() {
				    alert( "error" );
				  });
				
				}
				$('#calendar').fullCalendar('unselect');
			},
			editable: false,
			eventLimit: true,
			events: userEvents
		});
		
	});