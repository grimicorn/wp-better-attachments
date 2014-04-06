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
		'clean:cssDist',
		'sass:public',
		'sass:admin',
		'autoprefixer',
		'group_css_media_queries',
		'cssmin',
		'version'
	]);

	// JS
	grunt.registerTask('js', [
		'clean:jsDist',
		'uglify:public',
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

	// Run Dist process
	grunt.registerTask('build', [
		'clean:distCSS',
		'clean:distJS',
		'sass:publicDist',
		'sass:ltie9Dist',
		'sass:adminDist',
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
