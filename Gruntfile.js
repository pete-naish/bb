'use strict';
var path = require('path');
var lrSnippet = require('grunt-contrib-livereload/lib/utils').livereloadSnippet;

var folderMount = function folderMount(connect, point) {
	return connect.static(path.resolve(point));
};

module.exports = function(grunt) {
	// Do grunt-related things in here
	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		uglify: {
			options: {
				compress: true,
				mangle: {
					except: ['jQuery']
				},
				report: 'gzip',
				banner: '/* <%= grunt.template.today("HH:MM:ss dd/mm/yy") %> */\n'
			},
			build: {
				src: ['!js/main.min.js', 'js/*.js'],
				dest: 'js/main.min.js'
			}
		},
		watch: {
			scripts: {
				files: ['!js/main.min.js', 'js/*.js'],
				tasks: ['uglify']	
			},
			css: {
				files: ['css/*.scss'],
				tasks: ['sass']	
			}
			
		},
		sass: {
			dist: {
				files: {
					'css/main.css': 'css/main.scss'
				},
				options: {
					style: 'expanded',
					lineNumbers: true
				}
			}
		},
		connect: {
			livereload: {
				options: {
					port: 9001,
					middleware: function(connect, options) {
						return [lrSnippet, folderMount(connect, options.base)]
					}
				}
			}
		},
		// Configuration to be run (and then tested)
		regarde: {
			dist: {
				files: ['css/main.css', 'index.php'],
				tasks: ['livereload-start', 'livereload']
			}
		}
		// ,
		// concat: {
		// 	options: {
		// 		separator: ';'
		// 	},
		// 	dist: {
		// 		src: ['js/min/*.min.js'],
		// 		dest: 'js/main.min.js'
		// 	}
		// }

	});
	// Load the plugin that provides the "uglify" task. Concat files.
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-regarde');
	grunt.loadNpmTasks('grunt-contrib-connect');
	grunt.loadNpmTasks('grunt-contrib-livereload');
	
	// grunt.loadNpmTasks('grunt-contrib-concat');

	// Default task(s).
	grunt.registerTask('default', ['uglify', 'sass', 'watch', 'livereload-start', 'connect', 'regarde']);

};