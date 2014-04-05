module.exports = function(grunt) {
	// Measures the time each task takes
	require('time-grunt')(grunt);

	// Load Grunt Configurations
	require('load-grunt-config')(grunt);

	// Load Tasks
	// This will load all grunt-* tasks that are in the package.json devDependencies
	require('load-grunt-tasks')(grunt, { scope: 'devDependencies' });

	// Default task
	grunt.registerTask('default', [ 'watch' ]);

 // Setup task
	grunt.registerTask('setup', [ '' ]);

	// CSS
	grunt.registerTask('css', [
		'clean:distCSS',
		'sass:site',
		'sass:ltie9',
		'sass:admin',
		'autoprefixer',
		'group_css_media_queries',
		'cssmin',
		'version'
	]);

	// JS
	grunt.registerTask('js', [
		'clean:distJS',
		'uglify:site',
		'uglify:env',
		'uglify:admin',
		'version'
	]);

	// Cleans up conflicts
	grunt.registerTask('conflict', [
		'js',
		'css'
	]);

	// Run all SASS, Uglify, and related tasks
	grunt.registerTask('process', [
		'js',
		'css'
	]);

	// Run Build process
	grunt.registerTask('build', [
		'clean:distCSS',
		'clean:distJS',
		'sass:siteBuild',
		'sass:ltie9Build',
		'sass:adminBuild',
		'imageoptim:all',
		'uglify:build',
		'autoprefixer',
		'group_css_media_queries',
		'cssmin',
		'version',
		'phpdoc'
	]);

	// Distribution task
	grunt.registerTask('build', [ 'dist' ]);
};
