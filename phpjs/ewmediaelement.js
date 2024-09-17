// Create MediaElement.js
document.addEventListener('DOMContentLoaded', function() {
	var mediaElements = document.querySelectorAll('video, audio');
	for (var i = 0, total = mediaElements.length; i < total; i++) {
		new MediaElementPlayer(mediaElements[i], {
			autoRewind: true,
		//	features: ['playpause', 'current', 'progress', 'duration', 'volume', 'skipback', 'jumpforward', 'speed', 'fullscreen'],
			features: ['playpause', 'current', 'progress', 'duration', 'volume', 'speed', 'fullscreen'],
		});
	}
});
