module.exports =
	jsSourceMaps:
		files: [
			expand: true
			src: ["*.js.map"]
			dest: "assets/js/dist"
			filter: "isFile"
		]

	dist:
		files: [
			expand: true
			src:  [
					'**',
					'!node_modules/**',
					'!releases/**',
					'!.git/**',
					'!.sass-cache/**',
					'!**/scss/**',
					'!assets/js/src/**',
					'!screenshots/**',
					'!Gruntfile.js',
					'!package.json',
					'!.gitignore',
					'!README.md',
					'!LICENSE',
					'!phpdoc.dist.xml',
					'!st3-ignores.txt',
					'!grunt/**',
					'!docs/**',
					'!**/*.map',
					'!**/.DS_Store',
				],
			dest: 'releases/wp-better-attachments/'
		]