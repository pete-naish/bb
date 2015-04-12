var gulp = require('gulp'),
    sass = require('gulp-sass'),
    rename = require("gulp-rename"),
    autoprefixer = require("gulp-autoprefixer"),
    minifyCSS = require('gulp-minify-css'),
    livereload = require('gulp-livereload'),
    sourcemaps = require('gulp-sourcemaps');

function swallowError (error) {
    console.log(error.toString());
    this.emit('end');
}

gulp.task('sass', function() {
    return gulp.src('./css/*.scss')
        .pipe(sourcemaps.init())  
        .pipe(sass({
            outputStyle: 'compressed',
        }))
        .on('error', swallowError)
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