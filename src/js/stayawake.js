(async function () {
	let wakeLock = null;

	// Request a wakeLock.
	const requestWakeLock = async () => {
		try {
			wakeLock = await navigator.wakeLock.request();
			/*wakeLock.addEventListener("release", () => {
				console.log("released:", wakeLock.released);
			});
			console.log("released:", wakeLock.released);*/
		} catch (err) {
			wakeLock = null;
			//console.error(`${err.name}, ${err.message}`);
		}
	};

	// Request a screen wake lock on load.
	await requestWakeLock();

	const handleVisibilityChange = async () => {
		if (wakeLock !== null && document.visibilityState === "visible") {
			//console.log("regained visibility");
			await requestWakeLock();
		}
	};

	const handleInteraction = async () => {
		//console.log("checking interaction");
		if (wakeLock === null) {
			//console.log("has interacted");
			await requestWakeLock();
		}
	};

	/**
	 * Listeners for visibility change and interactions.
	 */
	document.addEventListener("visibilitychange", handleVisibilityChange);
	document.addEventListener("click", handleInteraction);
	document.addEventListener("touchend", handleInteraction);

	let isScrolling;
	document.addEventListener("scroll", () => {
		window.clearTimeout(isScrolling);
		isScrolling = setTimeout(function () {
			handleInteraction();
		}, 120);
	});
})();
