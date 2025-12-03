"use strict";

/* --------------------------------------------------
 * Plugins laden
 * -------------------------------------------------- */
const gulp         = require("gulp");
const plumber      = require("gulp-plumber");
const browsersync  = require("browser-sync").create();
const del          = require("del");
const rename       = require("gulp-rename");
const terser       = require("gulp-terser");
const header       = require("gulp-header");
const sassCompiler = require("gulp-sass")(require("sass"));
const cleanCSS     = require("gulp-clean-css");
const autoprefixer = require("gulp-autoprefixer");
const merge        = require("merge-stream");

// eigener HTML-Build (optional, wie gehabt)
const { buildAll } = require("./scripts/build-html");

// package.json laden für Banner
const pkg = require("./package.json");

/* --------------------------------------------------
 * Banner
 * -------------------------------------------------- */
const banner = [
  "/*!\n",
  ` * Start Bootstrap - ${pkg.title} v${pkg.version} (${pkg.homepage})\n`,
  ` * Copyright 2013-${new Date().getFullYear()} ${pkg.author}\n`,
  ` * Licensed under ${pkg.license} (https://github.com/StartBootstrap/${pkg.name}/blob/master/LICENSE)\n`,
  " */\n\n",
].join("");

/* --------------------------------------------------
 * BrowserSync starten
 * -------------------------------------------------- */
function browserSync(done) {
  browsersync.init({
    server: {
      baseDir: "./"
    },
    port: 3000
  });
  done();
}

/* --------------------------------------------------
 * HTML Task
 * -------------------------------------------------- */
async function html() {
  await buildAll();
  browsersync.reload();
}

/* --------------------------------------------------
 * BrowserSync Reload
 * -------------------------------------------------- */
function browserSyncReload(done) {
  browsersync.reload();
  done();
}

/* --------------------------------------------------
 * Vendor Ordner löschen
 * -------------------------------------------------- */
function clean() {
  return del(["./vendor/"]);
}

/* --------------------------------------------------
 * Third-party Dependencies kopieren
 * -------------------------------------------------- */
function modules() {
  // Bootstrap JS
  const bootstrapJS = gulp
    .src("./node_modules/bootstrap/dist/js/*")
    .pipe(gulp.dest("./vendor/bootstrap/js"));

  // Bootstrap SCSS
  const bootstrapSCSS = gulp
    .src("./node_modules/bootstrap/scss/**/*")
    .pipe(gulp.dest("./vendor/bootstrap/scss"));

  // ChartJS
  const chartJS = gulp
    .src("./node_modules/chart.js/dist/*.js")
    .pipe(gulp.dest("./vendor/chart.js"));

  // Datatables
  const dataTables = gulp
    .src([
      "./node_modules/datatables.net/js/*.js",
      "./node_modules/datatables.net-bs4/js/*.js"
    ])
    .pipe(gulp.dest("./vendor/datatables"));

  return merge(bootstrapJS, bootstrapSCSS, chartJS, dataTables);
}

/* --------------------------------------------------
 * CSS Task
 * -------------------------------------------------- */
function css() {
  return gulp
    .src("./scss/**/*.scss")
    .pipe(plumber())
    .pipe(
      sassCompiler({
        outputStyle: "expanded"
      }).on("error", sassCompiler.logError)
    )
    .pipe(autoprefixer())
    .pipe(gulp.dest("./css"))
    .pipe(cleanCSS())
    .pipe(
      rename({
        suffix: ".min"
      })
    )
    .pipe(gulp.dest("./css"))
    .pipe(browsersync.stream());
}

/* --------------------------------------------------
 * JS Task
 * -------------------------------------------------- */
function js() {
  return gulp
    .src([
      "./js/**/*.js",
      "!./js/**/*.min.js"
    ])
    .pipe(plumber())
    .pipe(terser()) // <<< moderner Minifier
    .pipe(header(banner, { pkg }))
    .pipe(
      rename({
        suffix: ".min"
      })
    )
    .pipe(gulp.dest("./js"))
    .pipe(browsersync.stream());
}

/* --------------------------------------------------
 * Watch Dateien
 * -------------------------------------------------- */
function watchFiles() {
  gulp.watch("./scss/**/*", css);
  gulp.watch(["./js/**/*.js", "!./js/**/*.min.js"], js);
  gulp.watch("./src/**/*.html", html);
  gulp.watch(["./*.html", "./img/**/*"], browserSyncReload);
}

/* --------------------------------------------------
 * Tasks definieren
 * -------------------------------------------------- */
const vendor = gulp.series(clean, modules);
const build  = gulp.series(vendor, gulp.parallel(css, js, html));
const watch  = gulp.series(build, gulp.parallel(watchFiles, browserSync));

/* --------------------------------------------------
 * Exports
 * -------------------------------------------------- */
exports.css    = css;
exports.js     = js;
exports.clean  = clean;
exports.vendor = vendor;
exports.build  = build;
exports.html   = html;
exports.watch  = watch;
exports.default = build;
