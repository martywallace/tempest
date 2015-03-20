module.exports = function(grunt) {

	var config = {
		// Path configuration.
		paths: {
			js: {
				app: 'js/app',
				vendor: 'js/vendor',
				tempest: 'js/tempest'
			}
		},

		// Append additional vendor JavaScript files here.
		vendorJs: [
			'<%= config.paths.js.vendor %>/jquery/dist/jquery.min.js',
			'<%= config.paths.js.vendor %>/modernizr/modernizr.js',
		],

		// SASS files to watch and compile.
		sassFiles: [{
			expand: true,
			cwd: 'sass',
			src: '**/*.scss',
			dest: 'public/css',
			ext: '.css'
		}]
	};

	grunt.initConfig({
		config: config,
		pkg: grunt.file.readJSON('package.json'),

		// Cleans up directories that grunt will compile files into.
		clean: {
			all: {
				src: [
					'public/' + config.paths.js.app,
					'public/' + config.paths.js.vendor,
					'public/' + config.paths.js.tempest
				]
			}
		},

		// Compile SASS files into CSS files.
		sass: {
			dev: {
				files: config.sassFiles
			},
			prod: {
				files: config.sassFiles,
				options: {
					style: 'compressed'
				}
			}
		},

		// Compile TypeScript.
		ts: {
			prod: {
				files: [{
					src: 'js/**/*.ts',
					dest: 'public/js'
				}],
				options: {
					sourceMap: false,
					fast: 'never'
				}
			}
		},

		// Watch for changes to files that will need to be compiled.
		watch: {
			sass: {
				files: 'sass/**/*.scss',
				tasks: ['sass:dev']
			},
			js: {
				files: 'js/**/*.js',
				tasks: ['ts:prod', 'merge']
			},
			ts: {
				files: 'js/**/*.ts',
				tasks: ['ts:prod', 'merge']
			}
		},

		// Concatenate generated JavaScript.
		concat: {
			vendor: {
				src: config.vendorJs,
				dest: 'public/js/vendor.js'
			},
			tempest: {
				src: [
					'js/tempest/**/*.js',
					'public/<%= config.paths.js.tempest %>/**/*.js'
				],
				dest: 'public/<%= config.paths.js.tempest %>.js'
			},
			app: {
				src: [
					'js/app/**/*.js',
					'public/<%= config.paths.js.app %>/**/*.js'
				],
				dest: 'public/<%= config.paths.js.app %>.js'
			}
		},

		// Minify result files for production.
		uglify: {
			js: {
				files: [{
					expand: true,
					cwd: 'public/js',
					src: '**/*.js',
					dest: 'public/js'
				}]
			}
		}
	});


	grunt.loadNpmTasks('grunt-ts');
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');

	grunt.registerTask('merge', ['concat', 'clean']);

	// Use during development - does not minify files & runs a watch task.
	grunt.registerTask('dev', ['sass:dev', 'ts:prod', 'merge', 'watch']);

	// Run on production - minifies files.
	grunt.registerTask('prod', ['sass:prod', 'ts:prod', 'merge', 'uglify']);

	// Default to development mode.
	grunt.registerTask('default', ['dev']);
}