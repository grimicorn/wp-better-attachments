module.exports =
	all:
		options:
			jpegMini: false
			imageAlpha: true
			quitAfter: true
		src: [
			"assets/img/**/*.{png,jpg,gif}"
			"assets/bower_components/**/*.{png,jpg,gif}"
		]