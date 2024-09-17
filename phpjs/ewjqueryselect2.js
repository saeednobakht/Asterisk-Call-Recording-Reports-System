// Create jquery select2
function ew_CreatejQuerySelect2(formid, id, theme) {
//	var cpEl = '#'+id;
	var cpEl = '[data-field="'+id+'"]';
	if ($(cpEl)) {
		$(cpEl).select2({
		    dir: EW_LANG_DIR,
			theme: theme
		});
	}
}
