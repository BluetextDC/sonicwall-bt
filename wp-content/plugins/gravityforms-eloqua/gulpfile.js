var gulp        = require('gulp'),
    sass        = require('gulp-sass'),
    rename      = require('gulp-rename'),
    cssmin      = require('gulp-minify-css'),
    concat      = require('gulp-concat'),
    uglify      = require('gulp-uglify'),
    jshint      = require('gulp-jshint'),
    scsslint    = require('gulp-sass-lint'),
    cache       = require('gulp-cached'),
    prefix      = require('gulp-autoprefixer'),
    //browserSync = require('browser-sync'),
    //reload      = browserSync.reload,
    size        = require('gulp-size'),
    plumber     = require('gulp-plumber'),
    deploy      = require('gulp-gh-pages'),
    notify      = require('gulp-notify');


gulp.task('scss', function() {
    var onError = function(err) {
      notify.onError({
          title:    "Gulp",
          subtitle: "Failure!",
          message:  "Error: <%= error.message %>",
          sound:    "Beep"
      })(err);
      this.emit('end');
  };

  return gulp.src('assets/scss/gfeloqua.scss')
    .pipe(plumber({errorHandler: onError}))
    .pipe(sass())
    .pipe(size({ gzip: true, showFiles: true }))
    .pipe(prefix())
    .pipe(rename('gfeloqua.css'))
    .pipe(gulp.dest('dist/css'))
    //.pipe(reload({stream:true}))
    .pipe(cssmin())
    .pipe(size({ gzip: true, showFiles: true }))
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('dist/css'))
});

/*
gulp.task('browser-sync', function() {
    browserSync({
        server: {
            baseDir: "dist/"
        }
    });
});
*/

gulp.task('deploy', function () {
    return gulp.src('dist/**/*')
        .pipe(deploy());
});

gulp.task('js', function() {
  gulp.src(['assets/scripts/gfeloqua.js','assets/scripts/gfeloqua-extensions.js'])
    .pipe(uglify())
    .pipe(size({ gzip: true, showFiles: true }))
    .pipe(concat('gfeloqua.min.js'))
    .pipe(gulp.dest('dist/js'));
    //.pipe(reload({stream:true}));
});

gulp.task('scss-lint', function() {
  gulp.src('assets/scss/**/*.scss')
    .pipe(cache('scsslint'))
    .pipe(scsslint());
});

gulp.task('jshint', function() {
  gulp.src('assets/scripts/*.js')
    .pipe(jshint())
    .pipe(jshint.reporter('default'));
});

gulp.task('watch', function() {
  gulp.watch('assets/scss/**/*.scss', ['scss']);
  gulp.watch('assets/scripts/*.js', ['jshint', 'js']);
});

gulp.task('default', [/*'browser-sync', */'js', 'scss', 'watch']);
