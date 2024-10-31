const mix = require("laravel-mix");

mix
	.sourceMaps(false, "source-map")
	.js("src/js/stayawake.js", "dist/js/stayawake.js")
	.options({
		processCssUrls: false,
	});
