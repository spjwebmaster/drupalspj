const { series, parallel, watch } = require('gulp');
var gulp = require('gulp');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
var sass = require('gulp-sass')(require('sass'));;
//sass.compiler = require('sass');



function clean(cb) {
  // body omitted
  cb();
  
}

function css(cb) {
  // body omitted
  watch('./scss/*.scss', { ignoreInitial: false }, function(cb) {
    // body omitted
    gulp.src('./scss/*.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest('./css/'));
    cb();
  });
}



exports.build = series(clean, parallel(css));