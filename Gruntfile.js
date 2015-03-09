module.exports = function(grunt) {

	var config = {
		vendorJs: [
			// Append additional vendor JavaScript files here.
			'js/vendor/jquery/dist/jquery.min.js'
		]
	};

	grunt.initConfig({
		globalConfig: config,
		pkg: grunt.file.readJSON('package.json'),

		// Cleans up directories that grunt will compile files into.
		clean: {
			all: {
				src: [
					'public/js/app',
					'public/js/vendor',
					'public/js/tempest'
				]
			}
		},

		// Compile SASS files into CSS files.
		sass: {
			dev: {
				files: [{
					expand: true,
					cwd: 'sass',
					src: ['**/*.scss'],
					dest: 'public/css',
					ext: '.css'
				}],
				options: {
					style: 'compressed'
				}
			},
			prod: {
				files: [{
					expand: true,
					cwd: 'sass',
					src: ['**/*.scss'],
					dest: 'public/css',
					ext: '.css'
				}]
			}
		},

		// Compile TypeScript.
		ts: {
			prod: {
				files: [{
					src: ['js/**/*.ts'],
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
				tasks: ['sass:prod', 'merge']
			},
			js: {
				files: 'js/**/*.js',
				tasks: ['merge']
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
				src: ['public/js/tempest/**/*.js'],
				dest: 'public/js/tempest.js'
			},
			app: {
				src: ['public/js/app/**/*.js'],
				dest: 'public/js/app.js'
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

	grunt.registerTask('dev', ['sass:dev', 'ts:prod', 'merge', 'uglify', 'watch']);
	grunt.registerTask('prod', ['sass:prod', 'ts:prod', 'merge']);

	grunt.registerTask('default', ['dev']);
}