'use strict';

const { src, dest, series, parallel, watch } = require('gulp');
const { spawn } = require('child_process');
const { Transform } = require('stream');

const sass = require('gulp-sass')(require('sass'));
const cleanCSS = require('gulp-clean-css');
const rename = require('gulp-rename');
const concat = require('gulp-concat');
const terser = require('gulp-terser');
const sourcemaps = require('gulp-sourcemaps');
const plumber = require('gulp-plumber');
const browserSync = require('browser-sync').create();

const CONFIG = {
  scss: {
    src: 'src/scss/main.scss',
    dest: 'static/assets/css',
    out: 'main.min.css'
  },
  js: {
    src: [
      'src/js/modules/cursor-light.js',
      'src/js/modules/favorites.js',
      'src/js/modules/favorites-page.js',
      'src/js/modules/modals.js',
      'src/js/modules/gallery-walk.js',
      'src/js/modules/view-in-room.js',
      'src/js/modules/ai-assistant.js',
      'src/js/modules/ai-curator.js',
      'src/js/modules/filters.js',
      'src/js/modules/journal.js',
      'src/js/modules/distorted-text.js',
      'src/js/modules/animations.js',
      'src/js/modules/demo-cart.js',
      'src/js/modules/checkout.js',
      'src/js/modules/recently-viewed.js',
      'src/js/modules/account.js',
      'src/js/modules/studio.js',
      'src/js/main.js'
    ],
    dest: 'static/assets/js',
    out: 'main.min.js'
  }
};

function html(done) {
  const child = spawn(process.execPath, ['scripts/build-html.js'], {
    stdio: 'inherit',
    shell: false
  });
  child.on('close', (code) => done(code ? new Error(`HTML build failed with code ${code}`) : null));
}

function images() {
  return src('src/images/**/*', { encoding: false })
    .pipe(dest('static/assets/images'));
}

function css() {
  return src(CONFIG.scss.src)
    .pipe(plumber())
    .pipe(sourcemaps.init())
    .pipe(sass({ outputStyle: 'expanded' }).on('error', sass.logError))
    .pipe(cleanCSS({ level: 2 }))
    .pipe(rename(CONFIG.scss.out))
    .pipe(sourcemaps.write('.'))
    .pipe(dest(CONFIG.scss.dest))
    .pipe(browserSync.stream());
}

function stripModuleSyntax() {
  return new Transform({
    objectMode: true,
    transform(file, enc, cb) {
      if (file.isBuffer()) {
        let contents = file.contents.toString(enc);
        contents = contents.replace(/^import\s.+from\s.+;?\s*$/gm, '');
        contents = contents.replace(/^export\s+default\s+/gm, '');
        contents = contents.replace(/^export\s+(function|const|let|var|class)\s+/gm, '$1 ');
        contents = contents.replace(/^export\s+\{[^}]*\};?\s*$/gm, '');
        file.contents = Buffer.from(contents, enc);
      }
      cb(null, file);
    }
  });
}

function js() {
  return src(CONFIG.js.src)
    .pipe(plumber())
    .pipe(sourcemaps.init())
    .pipe(stripModuleSyntax())
    .pipe(concat(CONFIG.js.out))
    .pipe(terser({
      compress: { drop_console: false },
      mangle: true
    }))
    .pipe(sourcemaps.write('.'))
    .pipe(dest(CONFIG.js.dest))
    .pipe(browserSync.stream());
}

function serve(done) {
  browserSync.init({
    server: 'static',
    port: 8095,
    open: false,
    notify: false,
    ui: false
  });
  done();
}

function reload(done) {
  browserSync.reload();
  done();
}

function watchFiles() {
  watch(['src/**/*.js', 'scripts/**/*.js'], series(html, js, reload));
  watch('src/scss/**/*.scss', css);
  watch('src/images/**/*', series(images, reload));
}

const build = series(html, parallel(css, js, images));
const dev = series(build, serve, watchFiles);

exports.html = html;
exports.css = css;
exports.js = js;
exports.images = images;
exports.build = build;
exports.watch = series(build, watchFiles);
exports.dev = dev;
exports.default = build;
