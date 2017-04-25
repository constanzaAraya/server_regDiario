'use strict';

var gulp = require('gulp'),
    babel = require('rollup-plugin-babel'),
    jshint = require('gulp-jshint'),
    sourcemaps = require('gulp-sourcemaps'),
    sass = require('gulp-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    browserSync = require('browser-sync').create(),
    source = require('vinyl-source-stream'),
    buffer = require('vinyl-buffer'),
    rename = require('gulp-rename'),
    rollup = require('rollup'),
    rollupUglify = require('rollup-plugin-uglify'),
    gulpif = require('gulp-if'),
    imagemin = require('gulp-imagemin'),
    nodeResolve = require('rollup-plugin-node-resolve'),
    commonjs = require('rollup-plugin-commonjs'),
    pug = require('gulp-pug'),
    gutil = require('gulp-util');


/**
 * CONFIG 
 */
var isRelease = false;

var baseDirs = {
    dist:'dist/',
    src:'src/',
    assets: 'dist/assets/'
};

/* routes: rutas */

var routes = {
    styles: {
        styl: baseDirs.src+'scss/main.scss',
        watch: baseDirs.src+'scss/**/*.scss',
        dest: baseDirs.assets+'css/',
        output: 'main.min.css'
    },

    templates: {
      pug: baseDirs.src+'templates/*.pug',
      _pug: baseDirs.src+'templates/_includes/*.pug',
    },

    scripts: {
        base:baseDirs.src+'js/main.js',
        js: baseDirs.src+'js/**/*.js',
        vendor: baseDirs.src+'js/vendor/**/*.js',
        jsmin: baseDirs.assets+'js/app.js'
    },

    files: {
        html: 'dist/',
        images: baseDirs.src+'images/*',
        imgmin: baseDirs.assets+'files/img/',
        cssFiles: baseDirs.assets+'css/*.css',
        htmlFiles: baseDirs.dist+'*.html',
        styleCss: baseDirs.assets+'css/main.min.css'
    }
};

/* Opciones de SASS */

var sassOptions = {
  errLogToConsole: true,
  sourceMap: true,
  outputStyle: 'expanded'
};


/**
 *  TASKS
 */

gulp.task('setIsRelease', function() {
  isRelease = true;
});


gulp.task('templates', function() {
  return gulp.src([routes.templates.pug, '!' + routes.templates._pug])
    .pipe(pug({
      pretty: true
      }).on('error', gutil.log))
    .pipe(gulp.dest(routes.files.html))
    .pipe(browserSync.stream());
});


gulp.task('rollup', function() {
  rollup.rollup({
    entry: routes.scripts.base,
    plugins: [
      nodeResolve({
        jsnext: true,
        main: true,
        browser: true
      }),
      commonjs({
        include: 'node_modules/**',
        sourceMap: true
      }),
      babel({
        exclude: 'node_modules/**'
      }),
      gulpif(isRelease, rollupUglify())
    ]
  })
  .then( function ( bundle ) {
    bundle.write({
      format: 'iife',
      dest: routes.scripts.jsmin,
      sourceMap: true
    });
  });
});


gulp.task('css', function() {
  return gulp.src(routes.styles.styl)
    .pipe(sass(sassOptions).on('error', sass.logError))
    .pipe(autoprefixer({
      browsers: ['last 2 versions', 'ie >= 9', 'and_chr >= 2.3']
    }).on('error', gutil.log))
    .pipe(rename(routes.styles.output))
    .pipe(gulp.dest(routes.styles.dest))
    .pipe(browserSync.stream());
});

/* Lint, lint the JavaScript files */

gulp.task('lint', function() {
  return gulp.src([routes.scripts.js, '!' + routes.scripts.vendor])
    .pipe(jshint({
      lookup: true,
      linter: 'jshint',
      esnext: true
    }).on('error', gutil.log))
    .pipe(jshint.reporter('default')); 
});

/* Image compressing task */

gulp.task('images', function() {
  gulp.src(routes.files.images)
    .pipe(imagemin().on('error', gutil.log))
    .pipe(gulp.dest(routes.files.imgmin));
});


gulp.task('server', ['build'], function() {
  browserSync.init({
    server: {
      baseDir: "./dist",
      middleware: function (req, res, next) {
        res.setHeader('Access-Control-Allow-Origin', '*');
        next();
      }
    }
  });

  gulp.watch(routes.styles.watch, ['css', 'images']);
  gulp.watch(routes.scripts.js, ['lint', 'rollup', 'images']);
  gulp.watch([routes.templates.pug, routes.templates._pug], ['templates', 'images'])
});


gulp.task('build', ['lint', 'rollup', 'css', 'images', 'templates']);
gulp.task('default', ['server']);
gulp.task('release', ['setIsRelease', 'build']);
