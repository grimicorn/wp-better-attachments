module.exports =
	multiple_files:
		expand: true
		flatten: true
		src: "assets/css/dist/*.css"
		dest: "assets/css/dist"

	sourcemap:
		options:
			map: true