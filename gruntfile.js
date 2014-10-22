'use strict';

module.exports = function (grunt) {
    // load all grunt tasks
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.initConfig({
        watch: {
            // if any .less file changes in directory "public/css/" run the "less"-task.
            files: ["less/app.less","less/bootstrap/*.less"],
            tasks: ["less:dev"]
        },
        // "less"-task configuration
        less: {
            dev: {
                options: {
                    paths: ["less/"]
                },
                files: {
                    "public/css/app.css": "less/app.less"
                }
            },
            dist:{
                options: {
                    paths: ["less/"],
                    cleancss: true
                },
                files: {
                    "public/css/app.css": "less/app.less"
                }
            }
        },
        copy: {
            less: {
                cwd: 'components/bootstrap/less',
                src: '**/*',
                dest: 'less/bootstrap',
                expand: true
            },
            js: {
                cwd: 'components/bootstrap/js',
                src: 'bootstrap.min.js',
                dest: 'public/js/vendor',
                expand: true
            },
            fonts: {
                cwd: 'components/bootstrap/fonts',
                src: '**/*',
                dest: 'public/fonts',
                expand: true
            },
            jquery: {
                cwd: 'components/jquery',
                src: 'jquery.min.js',
                dest: 'public/js/vendor',
                expand: true
            }

        }
    });
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('dist', ['less:dist']);
    grunt.registerTask('bootstrap',['copy:less','copy:js','copy:fonts','copy:jquery'])
};