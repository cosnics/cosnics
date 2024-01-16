(function () {
	document.addEventListener('visibilitychange', async () => {
		if (document.visibilityState === 'hidden') {
			const ajaxUri = getPath('WEB_PATH') + 'index.php';
			const formData = new FormData();
			formData.set('application', trackerContext);
			formData.set('go', 'leave_item');
			formData.set('tracker_id', trackerId);
			navigator.sendBeacon(ajaxUri, formData);
		}
	});
})();