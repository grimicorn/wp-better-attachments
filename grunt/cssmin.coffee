module.exports =
	admin:
		expand: true
		cwd: "assets/css/dist/"
		src: ["wpba-admin.*.min.css"]
		dest: "assets/css/dist"
		ext: ".min.css"

	site:
		expand: true
		cwd: "assets/css/dist/"
		src: ["wpba.*.min.css"]
		dest: "assets/css/dist"
		ext: ".min.css"