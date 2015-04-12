var gulp = require('gulp'),
    sass = require('gulp-sass'),
    rename = require("gulp-rename"),
    autoprefixer = require("gulp-autoprefixer"),
    minifyCSS = require('gulp-minify-css'),
    livereload = require('gulp-livereload'),
    sourcemaps = require('gulp-sourcemaps');

gulp.task('sass', function() {
    return gulp.src('./css/*.scss')
        .pipe(sourcemaps.init())  
            .pipe(sass({
                outputStyle: 'compressed',
            }))
            .pipe(autoprefixer({
                browsers: ['last 2 versions', 'ie 8'],
                cascade: false
            }))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('./css/'))
        .pipe(livereload());

});

gulp.task('watch', function() {
    livereload.listen();
    gulp.watch('./css/*.scss', ['sass']);
});