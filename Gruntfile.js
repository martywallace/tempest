module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		clean: {
			all: {
				src: ['public/css', 'public/js']
			}
		},
		sass: {
			dist: {
				files: [{
					expand: true,
					cwd: 'sass',
					src: ['*.scss'],
					dest: 'public/css',
					ext: '.css'
				}]
			}
		},
		watch: {
			sass: {
				files: 'sass/*.scss',
				tasks: ['default']
			}
		}
	});


	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('default', ['clean:all', 'sass:dist', 'watch:sass']);
}