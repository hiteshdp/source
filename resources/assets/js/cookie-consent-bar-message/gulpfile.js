var gulp = require('gulp');
var minify = require('gulp-minify');
var rename = require('gulp-rename');

gulp.task('compress', function() {
    gulp.src('src/*.js')
        .pipe(minify({
            noSource : true,
            ext:{
                min:'.min.js'
            }
        }))
        .pipe(gulp.dest('dist')); 
});

gulp.task('default', ['compress']);