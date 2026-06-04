'use strict';

const { src, dest, series, parallel, watch } = require('gulp');

const sass        = require('gulp-sass')( require('sass') );
const cleanCSS    = require('gulp-clean-css');
const rename      = require('gulp-rename');
const concat      = require('gulp-concat');
const terser      = require('gulp-terser');
const sourcemaps  = require('gulp-sourcemaps');
const plumber     = require('gulp-plumber');
const browserSync = require('browser-sync').create();

// ─── Config ───────────────────────────────────────────────────────────────────

const CONFIG = {
  // Change this to your local WordPress URL
  // Example: 'http://localhost/luma-gallery' or 'http://luma.local'
  proxy: 'http://luma-gallery.local/',

  scss: {
    src:  'src/scss/main.scss',
    dest: 'assets/css',
    out:  'main.min.css',
  },

  js: {
    // main.js must come first; modules are inlined via concatenation.
    // Note: ES module import/export syntax is stripped below — we concat then terser.
    // For a modern bundler, replace gulp-concat with rollup or esbuild.
    src:  [
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
      'src/js/main.js',
    ],
    dest: 'assets/js',
    out:  'main.min.js',
  },
};

// ─── SCSS task ────────────────────────────────────────────────────────────────

function buildCSS() {
  return src( CONFIG.scss.src )
    .pipe( plumber() )
    .pipe( sourcemaps.init() )
    .pipe( sass({ outputStyle: 'expanded' }).on( 'error', sass.logError ) )
    .pipe( cleanCSS({ level: 2 }) )
    .pipe( rename( CONFIG.scss.out ) )
    .pipe( sourcemaps.write( '.' ) )
    .pipe( dest( CONFIG.scss.dest ) )
    .pipe( browserSync.stream() );
}

// ─── JS task ──────────────────────────────────────────────────────────────────

// We strip ES module import/export lines before concatenating because
// gulp-concat does not understand ES modules. A rollup/esbuild build would
// handle this properly; this simple approach works for vanilla JS with
// explicit function exports used only within the same bundle.
const { Transform } = require('stream');

function stripModuleSyntax() {
  return new Transform({
    objectMode: true,
    transform( file, enc, cb ) {
      if ( file.isBuffer() ) {
        let contents = file.contents.toString( enc );
        // Remove import lines
        contents = contents.replace( /^import\s.+from\s.+;?\s*$/gm, '' );
        // Remove export keywords (keep the actual declaration)
        contents = contents.replace( /^export\s+default\s+/gm, '' );
        contents = contents.replace( /^export\s+(function|const|let|var|class)\s+/gm, '$1 ' );
        contents = contents.replace( /^export\s+\{[^}]*\};?\s*$/gm, '' );
        file.contents = Buffer.from( contents, enc );
      }
      cb( null, file );
    }
  });
}

function buildJS() {
  return src( CONFIG.js.src )
    .pipe( plumber() )
    .pipe( sourcemaps.init() )
    .pipe( stripModuleSyntax() )
    .pipe( concat( CONFIG.js.out ) )
    .pipe( terser({
      compress: { drop_console: false },
      mangle:   true,
    }) )
    .pipe( sourcemaps.write( '.' ) )
    .pipe( dest( CONFIG.js.dest ) )
    .pipe( browserSync.stream() );
}

// ─── BrowserSync ──────────────────────────────────────────────────────────────

function serve( done ) {
  browserSync.init({
    proxy:  CONFIG.proxy,  // <- change to your local WP URL
    open:   false,
    notify: false,
  });
  done();
}

// ─── Watch ────────────────────────────────────────────────────────────────────

function watchFiles() {
  watch( 'src/scss/**/*.scss', buildCSS );
  watch( 'src/js/**/*.js',    buildJS  );

  // Reload on PHP template changes
  watch( '**/*.php' ).on( 'change', browserSync.reload );
}

// ─── Exported tasks ───────────────────────────────────────────────────────────

const build = parallel( buildCSS, buildJS );
const dev   = series( build, parallel( serve, watchFiles ) );

exports.buildCSS = buildCSS;
exports.buildJS  = buildJS;
exports.build    = build;
exports.dev      = dev;
exports.watch    = series( build, watchFiles );
exports.default  = build;
