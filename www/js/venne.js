$(function() {
	$.fn.extend({
		triggerAndReturn: function (name, data) {
			var event = new $.Event(name);
			this.trigger(event, data);
			return event.result !== false;
		}
	});

	$('a[data-confirm], button[data-confirm], input[data-confirm]').live('click', function (e) {
		var el = $(this);
		if (el.triggerAndReturn('confirm')) {
			if (!confirm(el.attr('data-confirm'))) {
				e.preventDefault();
				e.stopImmediatePropagation();
				return false;
			}
		}
	});


	$('.dialog').dialog({
		autoOpen: true,
		modal: true,
		resizable: false,
		width: 800,
		maxWidth: 800,
		close: function(ev, ui) {
			$(this).remove();
		}
	});

});

