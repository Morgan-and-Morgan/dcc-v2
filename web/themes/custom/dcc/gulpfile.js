const gulp = require("gulp");
const sass = require('gulp-sass')(require('sass'));
const concat = require('gulp-concat');
const sourcemaps = require("gulp-sourcemaps");
const webpack = require("webpack-stream");

/** SASS TASKS **/
gulp.task("sass:build", function (cb) {
  gulp
    .src("./scss/style.scss", "./scss/custom/slick.scss")
    // .pipe(sourcemaps.init())
    .pipe(sass({ outputStyle: 'compressed' }).on("error", sass.logError))
    // .pipe(sourcemaps.write())
    // .pipe(concat('index.css'))
    .pipe(gulp.dest("./dist/css"));
  cb();
});

gulp.task("sass:watch", function (cb) {
  gulp.watch(["./scss/style.scss", "./scss/*/*.scss"], gulp.series("sass:build"));
  cb();
});

/** JS TASKS **/
gulp.task("js:build-main-js", function (cb) {
  gulp
    .src("./js/main.js")
    .pipe(
      webpack({
        //config options
        entry: "./js/main.js",
        output: {
          filename: "main-min.js"
        },
        mode: "production",
        optimization: {
          minimize: true,
        }
      })
    )
    .pipe(gulp.dest("./dist/js"));
  cb();
});

/* watch js directory for changes */
gulp.task("js:watch", function (cb) {
  gulp.watch(
    "./js/*.js",
    gulp.series(
      "js:build-main-js"
    )
  );
  cb();
});

/** ASSETS WATCH **/
gulp.task(
  "assets:watch",
  gulp.series(
    "sass:build",
    "sass:watch",
    "js:build-main-js",
    "js:watch"
  )
);

/** ASSETS BUILD **/
gulp.task(
  "assets:build",
  gulp.series(
    "sass:build",
    "js:build-main-js",
  )
);
