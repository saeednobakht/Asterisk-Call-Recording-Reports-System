// Create calendar
function ew_CreateCalendar(formid, id, format) {
	if (id.indexOf("$rowindex$") > -1)
		return;
	var $ = jQuery, el = ew_GetElement(id, formid), $el = $(el);
	if ($el.parent().is(".input-group"))
		return;
	var $btn = $('<button type="button"><span class="glyphicon glyphicon-calendar"></span></button>')
		.addClass("btn btn-default btn-sm").css({ "font-size": $el.css("font-size"), "height": $el.outerHeight() })
		.click(function() { $(this).closest(".has-error").removeClass("has-error"); });
	var settings = {
		inputField: el, // input field
		showsTime: / %H:%M(:%S)?$/.test(format), // shows time
		ifFormat: format, // date format
		button: $btn[0], // button
		cache: true // reuse the same calendar object, where possible
	};
	var args = {"id": id, "form": formid, "enabled": true, "settings": settings, "language": EW_LANGUAGE_ID};
	$el.wrap('<div class="input-group"></div>').after($('<span class="input-group-btn"></span>').append($btn));	
	$(function() {		
		$(document).trigger("calendar", [args]);
		if (!args.enabled)
			return;
		if (!Calendar._DN) {
			$.getScript(EW_RELATIVE_PATH + "calendar/lang/calendar-" + (args.language || "en") + ".js")
				.fail(function() {
					$.getScript(EW_RELATIVE_PATH + "calendar/lang/calendar-en.js");
				});
		}
		Calendar.setup(args.settings);
	});
}
