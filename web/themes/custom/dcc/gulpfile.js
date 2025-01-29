const gulp = require("gulp");
const sass = require("gulp-sass")(require("sass"));
const concat = require("gulp-concat");
const sourcemaps = require("gulp-sourcemaps");
const webpack = require("webpack-stream");

/** SASS TASKS **/
gulp.task("sass:build", function () {
  return gulp
    .src(["./scss/style.scss", "./scss/custom/slick.scss"]) // Use an array for multiple files
    // .pipe(sourcemaps.init()) // Uncomment if sourcemaps are required
    .pipe(sass({ outputStyle: "compressed" }).on("error", sass.logError))
    // .pipe(sourcemaps.write("./maps")) // Sourcemaps directory
    .pipe(gulp.dest("./dist/css"));
});

gulp.task("sass:watch", function () {
  return gulp.watch(["./scss/**/*.scss"], gulp.series("sass:build")); // Optimized glob pattern
});

/** JS TASKS **/
gulp.task("js:build-main-js", function () {
  return gulp
    .src("./js/main.js")
    .pipe(
      webpack({
        entry: "./js/main.js",
        output: { filename: "main-min.js" },
        mode: "production",
        optimization: {
          minimize: true,
        },
      })
    )
    .pipe(gulp.dest("./dist/js"));
});

gulp.task("js:watch", function () {
  return gulp.watch("./js/**/*.js", gulp.series("js:build-main-js")); // Optimized glob pattern
});

/** ASSETS WATCH **/
gulp.task(
  "assets:watch",
  gulp.series("sass:build", gulp.parallel("sass:watch", "js:watch"))
);

/** ASSETS BUILD **/
gulp.task("assets:build", gulp.series("sass:build", "js:build-main-js"));
