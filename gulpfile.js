"use strict";

// Load plugins
const autoprefixer = require("gulp-autoprefixer");
const cleanCSS = require("gulp-clean-css");
const del = require("del");
const gulp = require("gulp");
const merge = require("merge-stream");
const plumber = require("gulp-plumber");
const rename = require("gulp-rename");
const sass = require("gulp-sass");
const uglify = require("gulp-uglify");

// Clean public
function clean() {
  return del(['./public/*', '!./public/bundles', '!./public/index.php', '!./public/img', '!./public/fos']);
}

// Bring third party dependencies from node_modules into public directory
function modules() {
  // Bootstrap JS
  var bootstrapJS = gulp.src('./node_modules/bootstrap/dist/js/*')
    .pipe(gulp.dest('./public/js'));
  // Font Awesome CSS
  var fontAwesomeCSS = gulp.src('./node_modules/@fortawesome/fontawesome-free/css/all.css')
    .pipe(gulp.dest('./public/fontawesome/css'));
  // Font Awesome Fonts
  var fontAwesomeFonts = gulp.src('./node_modules/@fortawesome/fontawesome-free/webfonts/*')
    .pipe(gulp.dest('./public/fontawesome/webfonts'));
  // jQuery
  var jquery = gulp.src(['./node_modules/jquery/dist/*', '!./node_modules/jquery/dist/core.js'])
    .pipe(gulp.dest('./public/js'));
  // JQuery DatetimePickerJS
  var datetimePickerJS = gulp.src(['./node_modules/jquery-datetimepicker/build/jquery.datetimepicker.full.js'])
    .pipe(gulp.dest('./public/js'))
  // JQuery DatetimePickerCSS
  var datetimePickerCSS = gulp.src(['./node_modules/jquery-datetimepicker/jquery.datetimepicker.css'])
    .pipe(gulp.dest('./public/css'))
  return merge(bootstrapJS, fontAwesomeCSS, fontAwesomeFonts, jquery, datetimePickerJS, datetimePickerCSS);
}

// CSS task
function css() {
  return gulp
    .src("./assets/scss/**/*.scss")
    .pipe(plumber())
    .pipe(sass({
      outputStyle: "expanded",
      includePaths: "./node_modules",
    }))
    .on("error", sass.logError)
    .pipe(autoprefixer({
      cascade: false
    }))
    .pipe(gulp.dest("./public/css"))
    .pipe(rename({
      suffix: ".min"
    }))
    .pipe(cleanCSS())
    .pipe(gulp.dest("./public/css"));
}

// JS task
function js() {
  return gulp
    .src([
      './assets/js/*.js',
      '!./assets/js/*.min.js',
    ])
    .pipe(uglify())
    .pipe(rename({
      suffix: '.min'
    }))
    .pipe(gulp.dest('./public/js'));
}

// Watch files
function watchFiles() {
  gulp.watch("./assets/scss/**/*", css);
  gulp.watch(["./assets/js/**/*", "!./assets/js/**/*.min.js"], js);
}

// Define complex tasks
const vendor = gulp.series(clean, modules);
const build = gulp.series(vendor, gulp.parallel(css, js));
const watch = gulp.series(build, gulp.parallel(watchFiles));

// Export tasks
exports.css = css;
exports.js = js;
exports.clean = clean;
exports.vendor = vendor;
exports.build = build;
exports.watch = watch;
exports.default = build;
