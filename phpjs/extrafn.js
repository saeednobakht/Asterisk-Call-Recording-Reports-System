// Extra JavaScript for Xpanel 20+
//jQuery Selecting Elements By Searching Attribute Values Using Regular Expressions

jQuery.expr[':'].regex = function (elem, index, match) {
    var matchParams = match[3].split(','), validLabels = /^(data|css):/, attr = { method: matchParams[0].match(validLabels) ? matchParams[0].split(':')[0] : 'attr', property: matchParams.shift().replace(validLabels, '') }, regexFlags = 'ig', regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g, ''), regexFlags);
    return regex.test(jQuery(elem)[attr.method](attr.property));
}

/* plesk loader container start*/
function _body_onload() {
	loff();
	setFocus();
}

function _body_onunload() {
	lon();
}

function setFocus() {
	if (o = document.forms[0].username) {
		o.focus();
		o.select();
	}
}

function loff(target) {
	try {
		if (!target)
			target = this;
		target.document.getElementById("loaderContainer").style.display = "none";
	} catch (e) {
		return false;
	}
	return true;
}

function lon(target) {
	try {
		if (!target)
			target = this;
		if (!target._lon_disabled_arr)
			target._lon_disabled_arr = new Array();
		else if (target._lon_disabled_arr.length > 0)
			return true;
		target.document.getElementById("loaderContainer").style.display = "";
	} catch (e) {
		return false;
	}
	return true;
}

/* loading picture animation */
var _lanim_el;
var _lanim_frame;
var _lanim_frames;
var _lanim_frame_size;

function _lanim_start(frames, frame_size) {
	_lanim_el = document.getElementById('loaderAnimation');
	if (!_lanim_el)
		return;
	_lanim_frame = 0;
	_lanim_frames = frames;
        _lanim_frame_size = frame_size;
	setInterval('_lanim_proc()', 2000/_lanim_frames)
}

function _lanim_proc() {
	el = document.getElementById('loaderContainer');
	if (!el || el.style.display == 'none')
		return;
	_lanim_frame++;
	if (_lanim_frame >= _lanim_frames) _lanim_frame = 0
	_lanim_el.style.backgroundPosition = '0px -' + _lanim_frame_size*_lanim_frame + 'px';
}

/* plesk loader container finish*/

// Zerofilled value number
function zeroFill( number, width ) {
  width -= number.toString().length;
  if ( width > 0 )
  {
    return new Array( width + (/\./.test( number ) ? 2 : 1) ).join( '0' ) + number;
  }
  return number + ""; // always return a string
}

function getParameterByName(name, url = window.location.href) {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}
