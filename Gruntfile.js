module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		// Cleans up directories that grunt will compile files into.
		clean: {
			all: {
				src: ['public/css', 'public/js']
			}
		},

		// Compile SASS files into CSS files.
		sass: {
			dist: {
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
			default: {
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
				tasks: ['sass:dist']
			},
			ts: {
				files: 'js/**/*.ts',
				tasks: ['ts:default']
			}
		},

		// Concatenate generated CSS and JavaScript.
		concat: {
			css: {
				//
			},
			js: {
				//
			}
		}
	});


	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-ts');

	grunt.registerTask('default', ['clean:all', 'sass:dist', 'ts:default', 'watch']);
}