// Create color picker
function ew_CreateColorPicker(formid, id) {
	var cpEl = '#'+id;
	if ($(cpEl)) {
		$(cpEl).css('color', '#'+$(cpEl).val());
	//	$(cpEl).css('background-color', '#'+$(cpEl).val());
		$(cpEl).ColorPicker({
			onSubmit: function(hsb, hex, rgb, el) {
				$(el).val(hex);
				$(el).ColorPickerHide();
			},
			onBeforeShow: function () {
				$(this).ColorPickerSetColor(this.value);
			}
		})
		.bind('keyup', function(){
			$(this).ColorPickerSetColor(this.value);
		});
		$('.colorpicker_submit').click(function() {
		//	$(cpEl).css('color', 'white');
			$(cpEl).css('color', '#'+$(cpEl).val());
		//	$(cpEl).css('background-color', '#'+$(cpEl).val());
		});
	}
}

